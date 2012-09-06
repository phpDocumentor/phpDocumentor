<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

/**
 * Class diagram generator.
 *
 * Checks whether graphviz is enabled and logs an error if not.
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class Graph extends \phpDocumentor\Transformer\Writer\WriterAbstract
{
    /** @var string Name of the font to use to display the node labels with */
    protected $node_font = 'Courier';

    /**
     * Generates an array containing class to path references and then invokes
     * the Source specific method.
     *
     * @param \DOMDocument                        $structure      Structure source
     *     use as basis for the transformation.
     * @param \phpDocumentor\Transformer\Transformation $transformation Transformation
     *     that supplies the meta-data for this writer.
     *
     * @return void
     */
    public function transform(
        \DOMDocument $structure,
        \phpDocumentor\Transformer\Transformation $transformation
    ) {
        // NOTE: the -V flag sends output using STDERR and STDOUT
        exec('dot -V 2>&1', $output, $error);
        if ($error != 0) {
            $this->log(
                'Unable to find the `dot` command of the GraphViz package. '
                .'Is GraphViz correctly installed and present in your path?',
                \phpDocumentor\Plugin\Core\Log::ERR
            );
            return;
        }

        $this->node_font = $transformation->getParameter('font', 'Courier');

        // add to classes
        $xpath = new \DOMXPath($structure);
        $qry = $xpath->query('//class[full_name]/..');
        $class_paths = array();

        /** @var \DOMElement $element */
        foreach ($qry as $element) {
            $path = $element->getAttribute('generated-path');
            foreach ($element->getElementsByTagName('class') as $class) {
                $class_paths[
                    $class->getElementsByTagName('full_name')->item(0)->nodeValue
                ] = $path;
            }
        }

        // add to interfaces
        $qry = $xpath->query('//interface[full_name]/..');
        /** @var \DOMElement $element */
        foreach ($qry as $element) {
            $path = $element->getAttribute('generated-path');
            foreach ($element->getElementsByTagName('interface') as $class) {
                $class_paths[
                    $class->getElementsByTagName('full_name')->item(0)->nodeValue
                ] = $path;
            }
        }

        $this->class_paths = $class_paths;
        $type_method = 'process' . ucfirst($transformation->getSource());
        $this->$type_method($structure, $transformation);
    }

    /**
     * Builds a tree of namespace subgraphs with their classes associated.
     *
     * @param \phpDocumentor\GraphViz\Graph $graph               Graph to expand on.
     * @param \DOMElement             $namespace_element   Namespace index element.
     * @param \DOMXPath               $xpath               $xpath object to use
     *     for querying.
     * @param string                 $full_namespace_name unabbreviated version
     *     of the current namespace, namespace index only contains an abbreviated
     *     version and by building/passing this icnreases performance.
     *
     * @return void
     */
    public function buildNamespaceTree(\phpDocumentor\GraphViz\Graph $graph,
        \DOMElement $namespace_element, \DOMXPath $xpath, $full_namespace_name
    ) {
        $namespace = $namespace_element->getAttribute('name');
        $full_namespace_name .= '\\' . $namespace;
        $full_namespace_name = ltrim($full_namespace_name, '\\');

        $sub_graph = \phpDocumentor\GraphViz\Graph::create(
            'cluster_' . $full_namespace_name
        )
            ->setLabel($full_namespace_name != 'default' ? $namespace : '')
            ->setStyle('rounded')
            ->setColor($full_namespace_name != 'default' ? 'gray' : 'none')
            ->setFontColor('gray')
            ->setFontSize('11')
            ->setRankDir('LR');

        $sub_qry = $xpath->query(
            "/project/file/interface[@namespace='$full_namespace_name']"
            ."|/project/file/class[@namespace='$full_namespace_name']"
        );

        /** @var \DOMElement $sub_element */
        foreach ($sub_qry as $sub_element) {
            $node = \phpDocumentor\GraphViz\Node::create(
                $sub_element->getElementsByTagName('full_name')->item(0)->nodeValue,
                $sub_element->getElementsByTagName('name')->item(0)->nodeValue
            );

            $node->setShape('box');
            $node->setFontName($this->node_font);
            $node->setFontSize('11');

            if ($sub_element->getAttribute('abstract') == 'true') {
                $node->setLabel(
                    '<«abstract»<br/>'
                    . $sub_element->getElementsByTagName('name')->item(0)->nodeValue
                    . '>'
                );
            }

            $full_name = $sub_element->getElementsByTagName('full_name')
                ->item(0)->nodeValue;
            if (!isset($this->class_paths[$full_name])) {
                echo $full_name . PHP_EOL;
            } else {
                $node->setURL($this->class_paths[$full_name]);
                $node->setTarget('_parent');
            }

            $sub_graph->setNode($node);
        }

        $graph->addGraph($sub_graph);

        foreach ($namespace_element->getElementsByTagName('namespace') as $element) {
            $this->buildNamespaceTree(
                $sub_graph, $element, $xpath, $full_namespace_name
            );
        }
    }

    /**
     * Creates a class inheritance diagram.
     *
     * @param \DOMDocument                        $structure      Structure
     *     document used to gather data from.
     * @param \phpDocumentor\Transformer\Transformation $transformation Transformation
     *     element containing the meta-data.
     *
     * @return void
     */
    public function processClass(
        \DOMDocument $structure,
        \phpDocumentor\Transformer\Transformation $transformation
    ) {
        $filename = $transformation->getTransformer()->getTarget()
            . DIRECTORY_SEPARATOR . $transformation->getArtifact();
        $graph = \phpDocumentor\GraphViz\Graph::create()
                ->setRankSep('1.0')
                ->setCenter('true')
                ->setRank('source')
                ->setRankDir('RL')
                ->setSplines('true')
                ->setConcentrate('true');

        $xpath = new \DOMXPath($structure);
        $qry = $xpath->query("/project/namespace");

        /** @var \DOMElement $element */
        foreach ($qry as $element) {
            $this->buildNamespaceTree($graph, $element, $xpath, '');
        }

        // link all extended relations
        $qry = $xpath->query(
            '/project/file/interface[extends]|/project/file/class[extends]'
        );

        /** @var \DOMElement $element */
        foreach ($qry as $element) {
            $from_name = $element->getElementsByTagName('full_name')->item(0)
                ->nodeValue;

            foreach ($element->getElementsByTagName('extends') as $extends) {
                $to_name = $extends->nodeValue;

                if (!$to_name) {
                    continue;
                }

                $from = $graph->findNode($from_name);
                $to = $graph->findNode($to_name);

                if ($from === null) {
                    $from = \phpDocumentor\GraphViz\Node::create($from_name);
                    $from->setFontColor('gray');
                    $from->setLabel($from_name);
                    $graph->setNode($from);
                }

                if ($to === null) {
                    $to = \phpDocumentor\GraphViz\Node::create($to_name);
                    $to->setFontColor('gray');
                    $to->setLabel($to_name);
                    $graph->setNode($to);
                }

                $edge = \phpDocumentor\GraphViz\Edge::create($from, $to);
                $edge->setArrowHead('empty');
                $graph->link($edge);
            }
        }
        // link all implemented relations
        $qry = $xpath->query(
            '/project/file/interface[imports]|/project/file/class[implements]'
        );

        /** @var \DOMElement $element */
        foreach ($qry as $element) {
            $from_name = $element->getElementsByTagName('full_name')->item(0)
                ->nodeValue;

            foreach ($element->getElementsByTagName('implements') as $implements) {
                $to_name = $implements->nodeValue;

                if (!$to_name) {
                    continue;
                }

                $from = $graph->findNode($from_name);
                $to = $graph->findNode($to_name);

                if ($from === null) {
                    $from = \phpDocumentor\GraphViz\Node::create($from_name);
                    $from->setFontColor('gray');
                    $from->setLabel(addslashes($from_name));
                    $graph->setNode($from);
                }

                if ($to === null) {
                    $to = \phpDocumentor\GraphViz\Node::create($to_name);
                    $to->setFontColor('gray');
                    $to->setLabel(addslashes($to_name));
                    $graph->setNode($to);
                }

                $edge = \phpDocumentor\GraphViz\Edge::create($from, $to);
                $edge->setStyle('dotted');
                $edge->setArrowHead('empty');
                $graph->link($edge);
            }
        }

        $graph->export('svg', $filename);
    }

}
