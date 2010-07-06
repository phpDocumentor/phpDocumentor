<?php
/**
 * @author    mvriel
 * @copyright
 */

/**
 * Provide a short description for this class.
 *
 * @author     mvriel
 * @package
 * @subpackage
 */
class Nabu_Writer_Xslt_ClassGraph
{
  protected $target = './output';

  public function execute(DomDocument $xml)
  {
    // prepare the xsl document
    $xsl = new DOMDocument;
    $xsl->load('resources/class_overview.xsl');

    // configure the transformer
    $proc = new XSLTProcessor();
    $proc->importStyleSheet($xsl); // attach the xsl rules
    $path = realpath($this->target);

    $proc->setParameter('', 'title', 'Classes');
    $proc->transformToURI($xml, 'file://'.$path.'/classes.html');

    // generate graphviz
    $xpath = new DOMXPath($xml);
    $qry = $xpath->query("/project/file/class");

    $extend_classes = array();
    $classes = array();

    /** @var DOMElement $element */
    foreach ($qry as $element)
    {
      $extends = $element->getElementsByTagName('extends')->item(0)->nodeValue;
      if (!$extends)
      {
        $extends = 'stdClass';
      }

      if (!isset($extend_classes[$extends]))
      {
        $extend_classes[$extends] = array();
      }

      $extend_classes[$extends][] = $element->getElementsByTagName('name')->item(0)->nodeValue;
      $classes[] = $element->getElementsByTagName('name')->item(0)->nodeValue;
    }

    // find root nodes, (any class not found as extend)
    foreach ($extend_classes as $extend => $class_list)
    {
      if (!in_array($extend, $classes))
      {
        // if the extend is not in the list of classes (i.e. stdClass) then we have a root node
        $root_nodes[] = $extend;
      }
    }

    // traverse root nodes upwards
    $tree['stdClass'] = $this->buildTreenode($extend_classes);
    foreach ($root_nodes as $node)
    {
      if ($node === 'stdClass')
      {
        continue;
      }

      if (!isset($tree['stdClass']['?']))
      {
        $tree['stdClass']['?'] = array();
      }

      $tree['stdClass']['?'][$node] = $this->buildTreenode($extend_classes, $node);
    }

    $graph = new Image_GraphViz(true, array('rankdir' => 'RL', 'concentrate' => 'true', 'ratio' => '0.7'), 'Classes');
    $this->buildGraphNode($graph, $tree);
    $dot_file = $graph->saveParsedGraph();
    $graph->renderDotFile($dot_file, $path.'/classes.svg');
  }

  function buildTreenode($node_list, $parent = 'stdClass')
  {
    if (!isset($node_list[$parent]))
    {
      return array();
    }

    $result = array();
    foreach($node_list[$parent] as $node)
    {
      $result[$node] = $this->buildTreenode($node_list, $node);
    }

    return $result;
  }

  function buildGraphNode(Image_GraphViz $graph, $nodes, $parent = null)
  {
    foreach($nodes as $node => $children)
    {
      $graph->addNode($node, array('label' => $node, 'shape' => 'box'));
      if ($parent !== null)
      {
        $graph->addEdge(array($node => $parent), array('arrowhead' => 'empty', 'minlen' => '2'));
      }
      $this->buildGraphNode($graph, $children, $node);
    }
  }

}
