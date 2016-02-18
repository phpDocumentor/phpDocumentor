<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Renderer\Template\Action;

use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Graph as GraphVizGraph;
use phpDocumentor\GraphViz\Node;
use phpDocumentor\DomainModel\Renderer\Template\Action;
use phpDocumentor\DomainModel\Renderer\Template\ActionHandler;

class GraphHandler implements ActionHandler
{
    /** @var string Name of the font to use to display the node labels with */
    protected $nodeFont = 'Courier';

    /** @var Node[] a cache where nodes for classes, interfaces and traits are stored for reference */
    protected $nodeCache = array();

    /** @var Analyzer */
    private $analyzer;

    public function __construct(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * Executes the activities that this Action represents.
     *
     * @param Action|Graph $action
     *
     * @return void
     */
    public function __invoke(Action $action)
    {
        $project = $this->analyzer->getProjectDescriptor();
        try {
            $this->checkIfGraphVizIsInstalled();
        } catch (\Exception $e) {
            echo $e->getMessage();

            return;
        }

        $graph = GraphVizGraph::create()
            ->setRankSep('1.0')
            ->setCenter('true')
            ->setRank('source')
            ->setRankDir('RL')
            ->setSplines('true')
            ->setConcentrate('true');

        $this->buildNamespaceTree($graph, $project->getNamespace());

        $classes    = $project->getIndexes()->get('classes', new Collection())->getAll();
        $interfaces = $project->getIndexes()->get('interfaces', new Collection())->getAll();
        $traits     = $project->getIndexes()->get('traits', new Collection())->getAll();

        /** @var ClassDescriptor[]|InterfaceDescriptor[]|TraitDescriptor[] $containers  */
        $containers = array_merge($classes, $interfaces, $traits);

        foreach ($containers as $container) {
            $from_name = $container->getFullyQualifiedStructuralElementName();

            $parents     = array();
            $implemented = array();
            if ($container instanceof ClassDescriptor) {
                if ($container->getParent()) {
                    $parents[] = $container->getParent();
                }
                $implemented = $container->getInterfaces()->getAll();
            }
            if ($container instanceof InterfaceDescriptor) {
                $parents = $container->getParent()->getAll();
            }

            /** @var string|ClassDescriptor|InterfaceDescriptor $parent */
            foreach ($parents as $parent) {
                $edge = $this->createEdge($graph, $from_name, $parent);
                $edge->setArrowHead('empty');
                $graph->link($edge);
            }

            /** @var string|ClassDescriptor|InterfaceDescriptor $parent */
            foreach ($implemented as $parent) {
                $edge = $this->createEdge($graph, $from_name, $parent);
                $edge->setStyle('dotted');
                $edge->setArrowHead('empty');
                $graph->link($edge);
            }
        }

        $destination = $action->getRenderContext()->getDestination() . '/' . $action->getDestination();
        if (! file_exists(dirname($destination))) {
            @mkdir(dirname($destination), 0777, true);
        }
        $graph->export('svg', $destination);
    }

    /**
     * Creates a GraphViz Edge between two nodes.
     *
     * @param Graph  $graph
     * @param string $from_name
     * @param string|ClassDescriptor|InterfaceDescriptor|TraitDescriptor $to
     *
     * @return Edge
     */
    protected function createEdge($graph, $from_name, $to)
    {
        $to_name = !is_string($to) ? $to->getFullyQualifiedStructuralElementName() : $to;

        if (!isset($this->nodeCache[$from_name])) {
            $this->nodeCache[$from_name] = $this->createEmptyNode($from_name, $graph);
        }
        if (!isset($this->nodeCache[$to_name])) {
            $this->nodeCache[$to_name] = $this->createEmptyNode($to_name, $graph);
        }

        return Edge::create($this->nodeCache[$from_name], $this->nodeCache[$to_name]);
    }

    /**
     * @param string $name
     * @param Graph $graph
     *
     * @return Node
     */
    protected function createEmptyNode($name, $graph)
    {
        $node = Node::create($name);
        $node->setFontColor('gray');
        $node->setLabel($name);
        $graph->setNode($node);

        return $node;
    }

    /**
     * Builds a tree of namespace subgraphs with their classes associated.
     *
     * @param GraphVizGraph       $graph
     * @param NamespaceDescriptor $namespace
     *
     * @return void
     */
    protected function buildNamespaceTree(GraphVizGraph $graph, NamespaceDescriptor $namespace)
    {
        $full_namespace_name = $namespace->getFullyQualifiedStructuralElementName();
        if ($full_namespace_name == '\\') {
            $full_namespace_name = 'Global';
        }

        $sub_graph = GraphVizGraph::create('cluster_' . $full_namespace_name)
            ->setLabel($namespace->getName() == '\\' ? 'Global' : $namespace->getName())
            ->setStyle('rounded')
            ->setColor('gray')
            ->setFontColor('gray')
            ->setFontSize('11')
            ->setRankDir('LR');

        $elements = array_merge(
            $namespace->getClasses()->getAll(),
            $namespace->getInterfaces()->getAll(),
            $namespace->getTraits()->getAll()
        );

        /** @var ClassDescriptor|InterfaceDescriptor|TraitDescriptor $sub_element */
        foreach ($elements as $sub_element) {
            $node = Node::create($sub_element->getFullyQualifiedStructuralElementName(), $sub_element->getName())
                ->setShape('box')
                ->setFontName($this->nodeFont)
                ->setFontSize('11');

            if ($sub_element instanceof ClassDescriptor && $sub_element->isAbstract()) {
                $node->setLabel('<«abstract»<br/>' . $sub_element->getName(). '>');
            }

            //$full_name = $sub_element->getFullyQualifiedStructuralElementName();
            //$node->setURL($this->class_paths[$full_name]);
            //$node->setTarget('_parent');

            $this->nodeCache[$sub_element->getFullyQualifiedStructuralElementName()] = $node;
            $sub_graph->setNode($node);
        }

        foreach ($namespace->getChildren()->getAll() as $element) {
            $this->buildNamespaceTree($sub_graph, $element);
        }

        $graph->addGraph($sub_graph);
    }

    /**
     * Checks whether GraphViz is installed and throws an Exception otherwise.
     *
     * @throws \RuntimeException if graphviz is not found.
     *
     * @return void
     */
    protected function checkIfGraphVizIsInstalled()
    {
        // NOTE: the -V flag sends output using STDERR and STDOUT
        exec('dot -V 2>&1', $output, $error);
        if ($error != 0) {
            throw new \RuntimeException(
                'Unable to find the `dot` command of the GraphViz package. '
                . 'Is GraphViz correctly installed and present in your path?'
            );
        }
    }
}
