<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Class diagram generator.
 *
 * Checks whether graphviz is enabled and logs an error if not.
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Transformer_Writer_Graph
    extends DocBlox_Transformer_Writer_Abstract
{
    protected $has_namespaces = false;

    /**
     * Generates an array containing class to path references and then invokes the Source specific method.
     *
     * @param DOMDocument                        $structure
     * @param DocBlox_Transformer_Transformation $transformation
     *
     * @return void
     */
    public function transform(DOMDocument $structure, DocBlox_Transformer_Transformation $transformation)
    {
        // NOTE: the -V flag sends output using STDERR and STDOUT
        exec('dot -V 2>&1', $output, $error);
        if ($error != 0) {
            $this->log(
                'Unable to find the `dot` command of the GraphViz package. Is GraphViz correctly installed and present in your path?',
                Zend_Log::ERR
            );
          return;
        }

        // add to classes
        $xpath = new DOMXPath($structure);
        $qry = $xpath->query("/namespace[@name and @name != 'default']");
        if ($qry->length > 0) {
            $this->has_namespaces = true;
        }

        $qry = $xpath->query('//class[full_name]/..');
        $class_paths = array();

        /** @var DOMElement $element */
        foreach ($qry as $element) {
            $path = $element->getAttribute('generated-path');
            foreach ($element->getElementsByTagName('class') as $class) {
                $class_paths[$class->getElementsByTagName('full_name')->item(0)->nodeValue] = $path;
            }
        }

        // add to interfaces
        $qry = $xpath->query('//interface[full_name]/..');
        /** @var DOMElement $element */
        foreach ($qry as $element) {
            $path = $element->getAttribute('generated-path');
            foreach ($element->getElementsByTagName('interface') as $class) {
                $class_paths[$class->getElementsByTagName('full_name')->item(0)->nodeValue] = $path;
            }
        }

        $this->class_paths = $class_paths;
        $type_method = 'process'.ucfirst($transformation->getSource());
        $this->$type_method($structure, $transformation);
    }

    /**
     * Builds a tree of namespace subgraphs with their classes associated.
     *
     * @param DocBlox_GraphViz_Graph $graph
     * @param DOMElement             $namespace_element
     * @param DOMXPath               $xpath
     * @param string                 $full_namespace_name
     *
     * @return void
     */
    public function buildNamespaceTree(DocBlox_GraphViz_Graph $graph,
        DOMElement $namespace_element, DOMXPath $xpath, $full_namespace_name)
    {
        $namespace = $namespace_element->getAttribute('name');
        $full_namespace_name .= '\\'.$namespace;
        $full_namespace_name = ltrim($full_namespace_name, '\\');

        $sub_graph = DocBlox_GraphViz_Graph::create('cluster_' . str_replace(array('\\', '$'), '_', $full_namespace_name))
            ->setLabel($full_namespace_name != 'default' ? $namespace : '')
            ->setStyle('rounded')
            ->setColor($full_namespace_name != 'default' ? 'gray' : 'none')
            ->setFontColor('gray')
            ->setFontSize('11')
            ->setRankDir('LR');

        $sub_qry = $xpath->query(
            "/project/file/interface[@namespace='$full_namespace_name']|/project/file/class[@namespace='$full_namespace_name']"
        );

        /** @var DOMElement $sub_element */
        foreach ($sub_qry as $sub_element) {
            $node = DocBlox_GraphViz_Node::create(
                str_replace(array('\\', '$'), '_', $sub_element->getElementsByTagName('full_name')->item(0)->nodeValue),
                $sub_element->getElementsByTagName('name')->item(0)->nodeValue
            );
            $node->setShape('box');
            $node->setFontName('Courier New');
            $node->setFontSize('11');
            if ($sub_element->getAttribute('abstract') == 'true') {
                $node->setLabel('<«abstract»<br/>'. $sub_element->getElementsByTagName('name')->item(0)->nodeValue.'>');
            }
            if (!isset($this->class_paths[$sub_element->getElementsByTagName('full_name')->item(0)->nodeValue])) {
                echo $sub_element->getElementsByTagName('full_name')->item(0)->nodeValue.PHP_EOL;
            } else {
                $node->setURL($this->class_paths[$sub_element->getElementsByTagName('full_name')->item(0)->nodeValue]);
                $node->setTarget('_parent');
            }
            $sub_graph->setNode($node);
        }

        $graph->addGraph($sub_graph);

        foreach($namespace_element->getElementsByTagName('namespace') as $element) {
            $this->buildNamespaceTree($sub_graph, $element, $xpath, $full_namespace_name);
        }
    }

    /**
     * Creates a class inheritance diagram.
     *
     * @param DOMDocument                        $structure
     * @param DocBlox_Transformer_Transformation $transformation
     *
     * @return void
     */
    public function processClass(DOMDocument $structure, DocBlox_Transformer_Transformation $transformation)
    {
        $filename = $transformation->getTransformer()->getTarget() . DIRECTORY_SEPARATOR . $transformation->getArtifact();
        $graph = DocBlox_GraphViz_Graph::create()
            ->setRankSep('1.0')
            ->setCenter('true')
            ->setRank('source')
            ->setRankDir('RL')
            ->setSplines('true')
            ->setConcentrate('true');

        $xpath = new DOMXPath($structure);
        $qry = $xpath->query("/project/namespace");

        /** @var DOMElement $element */
        foreach($qry as $element) {
            $this->buildNamespaceTree($graph, $element, $xpath, '');
        }

        // link all extended relations
        $qry = $xpath->query('/project/file/interface[extends]|/project/file/class[extends]');

        /** @var DOMElement $element */
        foreach($qry as $element) {
            $from_name = $element->getElementsByTagName('full_name')->item(0)->nodeValue;
            $to_name = $element->getElementsByTagName('extends')->item(0)->nodeValue;

            if (!$to_name) {
                continue;
            }

            $from = $graph->findNode(str_replace(array('\\', '$'), '_', $from_name));
            $to = $graph->findNode(str_replace(array('\\', '$'), '_', $to_name));

            if ($from === null) {
                $from = DocBlox_GraphViz_Node::create(
                    str_replace(array('\\', '$'), '_', $from_name)
                );
                $from->setFontColor('gray');
                $from->setLabel(addslashes($from_name));
                $graph->setNode($from);
            }

            if ($to === null) {
                $to = DocBlox_GraphViz_Node::create(
                    str_replace(array('\\', '$'), '_', $to_name)
                );
                $to->setFontColor('gray');
                $to->setLabel(addslashes($to_name));
                $graph->setNode($to);
            }

            $edge = DocBlox_GraphViz_Edge::create($from, $to);
            $edge->setArrowHead('empty');
            $graph->link($edge);
        }

        // link all implemented relations
        $qry = $xpath->query('/project/file/interface[imports]|/project/file/class[implements]');

        /** @var DOMElement $element */
        foreach($qry as $element) {
            $from_name = $element->getElementsByTagName('full_name')->item(0)->nodeValue;

            foreach($element->getElementsByTagName('implements') as $implements) {
                $to_name = $implements->nodeValue;

                if (!$to_name) {
                    continue;
                }

                $from = $graph->findNode(str_replace(array('\\', '$'), '_', $from_name));
                $to = $graph->findNode(str_replace(array('\\', '$'), '_', $to_name));

                if ($from === null)
                {
                    $from = DocBlox_GraphViz_Node::create(str_replace(array('\\', '$'), '_', $from_name));
                    $from->setFontColor('gray');
                    $from->setLabel(addslashes($from_name));
                    $graph->setNode($from);
                }

                if ($to === null) {
                    $to = DocBlox_GraphViz_Node::create(str_replace(array('\\', '$'), '_', $to_name));
                    $to->setFontColor('gray');
                    $to->setLabel(addslashes($to_name));
                    $graph->setNode($to);
                }

                $edge = DocBlox_GraphViz_Edge::create($from, $to);
                $edge->setStyle('dotted');
                $edge->setArrowHead('empty');
                $graph->link($edge);
            }
        }

        $graph->export('svg', $filename);

        $svg = simplexml_load_file($filename);
        $script = $svg->addChild('script');
        $script->addAttribute('xlink:href', 'js/SVGPan.js', 'http://www.w3.org/1999/xlink');

        // for the SVGPan file to work no viewBox may be defined and the id of the first <g> node must be renamed to 'viewport'
        unset($svg['viewBox']);
        $svg->g['id'] = 'viewport';
        $svg->asXML($filename);
    }

}
