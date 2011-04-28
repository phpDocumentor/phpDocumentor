<?php
/**
 * DocBlox
 *
 * @category  DocBlox
 * @package   Writers
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com) Copyright (c)
 */

/**
 * Class diagram generator.
 *
 * Checks whether graphviz is enabled and logs an error if not.
 *
 * @category DocBlox
 * @package  Writers
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Transformer_Writer_Graph extends DocBlox_Transformer_Writer_Abstract
{
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
     * Generates a SVG Class Diagram at the given artifact location.
     *
     * @param DOMDocument                        $structure
     * @param DocBlox_Transformer_Transformation $transformation
     *
     * @todo this method should be refactored into smaller components.
     *
     * @return void
     */
    public function processClass(DOMDocument $structure, DocBlox_Transformer_Transformation $transformation)
    {
        $filename = $transformation->getTransformer()->getTarget() . DIRECTORY_SEPARATOR . $transformation->getArtifact();

        // generate graphviz
        $xpath = new DOMXPath($structure);
        $qry = $xpath->query("/project/file/class|/project/file/interface");

        $extend_classes = array();
        $classes = array();

        /** @var DOMElement $element */
        foreach ($qry as $element) {
            $extends = $element->getElementsByTagName('extends')->item(0)->nodeValue;
            if (!$extends) {
                $extends = 'stdClass';
            }

            if (!isset($extend_classes[$extends])) {
                $extend_classes[$extends] = array();
            }

            $extend_classes[$extends][] = $element->getElementsByTagName('full_name')->item(0)->nodeValue;
            $classes[] = $element->getElementsByTagName('full_name')->item(0)->nodeValue;
        }

        // find root nodes, (any class not found as extend)
        foreach ($extend_classes as $extend => $class_list) {
            if (!in_array($extend, $classes)) {
                // if the extend is not in the list of classes (i.e. stdClass) then we have a root node
                $root_nodes[] = $extend;
            }
        }

        if (empty($root_nodes)) {
            $this->log('No classes have been found, and therefore no class diagram is required', Zend_Log::INFO);
            return;
        }

        // traverse root nodes upwards
        $tree['stdClass'] = $this->buildTreenode($extend_classes);
        foreach ($root_nodes as $node) {
            if ($node === 'stdClass') {
                continue;
            }

            if (!isset($tree['stdClass']['?'])) {
                $tree['stdClass']['?'] = array();
            }

            $tree['stdClass']['?'][$node] = $this->buildTreenode($extend_classes, $node);
        }

        $graph = new Image_GraphViz(
            true,
            array(
                'rankdir' => 'RL',
                'splines' => true,
                'concentrate' => 'true',
                'ratio' => '0.9',
            ),
            'Classes'
        );
        $this->buildGraphNode($graph, $tree);

        // disable E_STRICT reporting on the end to prevent PEAR from throwing Strict warnings.
        $reporting = error_reporting();
        error_reporting(error_reporting() & ~E_STRICT);

        // render graph using Image_GraphViz
        $dot_file = $graph->saveParsedGraph();
        $graph->renderDotFile($dot_file, $filename);
        error_reporting($reporting);

        // add panning and zooming extension
        $svg = simplexml_load_file($filename);
        $script = $svg->addChild('script');
        $script->addAttribute('xlink:href', 'js/SVGPan.js', 'http://www.w3.org/1999/xlink');

        // for the SVGPan file to work no viewBox may be defined and the id of the first <g> node must be renamed to 'viewport'
        unset($svg['viewBox']);
        $svg->g['id'] = 'viewport';
        // save a full version
        // $svg->asXML(substr($filename, 0, -4) . '_full.svg');

        // replace width and height with 100% on non-full version
        // $svg['width']  = '100%';
        // $svg['height'] = '100%';
        $svg->asXML($filename);
    }

    /**
     * Recursive method which builds the tree to use in the Class Diagram creation.
     *
     * @param array  $node_list
     * @param string $parent
     * @param array  $chain
     *
     * @return array
     */
    protected function buildTreenode(array $node_list, $parent = 'stdClass', $chain = array())
    {
        if (count($chain) > 50) {
            $path = implode(' => ', array_slice($chain, -10));
            $this->log(
                'Maximum nesting level reached of 50, last 10 classes in the hierarchy path: '.$path,
                Zend_Log::WARN
            );
            return array();
        }

        if (!isset($node_list[$parent])) {
            return array();
        }

        $result = array();
        $chain[] = $parent;
        foreach ($node_list[$parent] as $node) {
            $result[$node] = $this->buildTreenode($node_list, $node, $chain);
        }

        return $result;
    }

    /**
     * Recursive method which builds the actual node for use in the Image_Graphviz object.
     *
     * @param Image_GraphViz $graph
     * @param array          $nodes
     * @param array|null     $parent
     *
     * @return void
     */
    protected function buildGraphNode(Image_GraphViz $graph, $nodes, $parent = null)
    {
        foreach ($nodes as $node => $children) {
            $node_array = explode('\\', $node);

            $properties = array('label' => end($node_array), 'shape' => 'box', 'style' => 'filled', 'fillcolor' => 'white');
            if (isset($this->class_paths[$node])) {
                $properties['URL'] = $this->class_paths[$node];
                $properties['target'] = '_top';
            } else {
                $properties['fontcolor'] = 'gray';
            }
            $graph->addNode(md5($node), $properties);

            if ($parent !== null) {
                $graph->addEdge(array(md5($node) => md5($parent)), array('arrowhead' => 'empty', 'minlen' => '2'));
            }
            $this->buildGraphNode($graph, $children, $node);
        }
    }
}
