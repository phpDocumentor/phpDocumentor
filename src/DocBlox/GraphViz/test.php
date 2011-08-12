<?php
include('Graph.php');
include('Edge.php');
include('Node.php');
include('Attribute.php');

$graph = DocBlox_GraphViz_Graph::create();
echo $graph
  ->addGraph(
    $graph2 = DocBlox_GraphViz_Graph::create('cluster_bla')
      ->setNode(DocBlox_GraphViz_Node::create('node3'))
  )
  ->setNode(
    DocBlox_GraphViz_Node::create('node1')
      ->setStyle('filled')
      ->setColor('lightgrey')
      ->setFontSize('20')
  )
  ->setNode(DocBlox_GraphViz_Node::create('node2'))
  ->link(DocBlox_GraphViz_Edge::create($graph->node1, $graph->node2))
  ->link(DocBlox_GraphViz_Edge::create($graph->node1, $graph2->node3))
  ->link(DocBlox_GraphViz_Edge::create($graph2->node3, $graph->node2));
$graph->export('png', 'test2.png');