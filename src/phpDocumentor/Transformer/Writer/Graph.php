<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Graph as GraphVizGraph;
use phpDocumentor\GraphViz\Node;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use Zend\Stdlib\Exception\ExtensionNotLoadedException;

/**
 * Writer responsible for generating various graphs.
 *
 * The Graph writer is capable of generating a Graph (as provided using the 'source' parameter) at the location provided
 * using the artifact parameter.
 *
 * Currently supported:
 *
 * * 'class', a Class Diagram Generated using GraphViz
 *
 * @todo Fix this class
 */
class Graph extends WriterAbstract
{
    /** @var string Name of the font to use to display the node labels with */
    protected $nodeFont = 'Courier';

    /** @var Node[] a cache where nodes for classes, interfaces and traits are stored for reference */
    protected $nodeCache = [];

    /** @var GraphVizGraph[] */
    protected $namespaceCache = [];

    /**
     * Invokes the query method contained in this class.
     *
     * @param ProjectDescriptor $project        Document containing the structure.
     * @param Transformation    $transformation Transformation to execute.
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation)
    {
        $type_method = 'process' . ucfirst($transformation->getSource());
        $this->{$type_method}($project, $transformation);
    }

    /**
     * Creates a class inheritance diagram.
     */
    public function processClass(ProjectDescriptor $project, Transformation $transformation)
    {
        try {
            $this->checkIfGraphVizIsInstalled();
        } catch (\Exception $e) {
            echo $e->getMessage();

            return;
        }

        if ($transformation->getParameter('font') !== null && $transformation->getParameter('font')->getValue()) {
            $this->nodeFont = $transformation->getParameter('font')->getValue();
        } else {
            $this->nodeFont = 'Courier';
        }

        $filename = $this->getDestinationPath($transformation);

        $graph = GraphVizGraph::create()
            ->setRankSep('1.0')
            ->setCenter('true')
            ->setRank('source')
            ->setRankDir('RL')
            ->setSplines('true')
            ->setConcentrate('true');

        $this->buildNamespaceTree($graph, $project->getNamespace());

        $classes = $project->getIndexes()->get('classes', new Collection())->getAll();
        $interfaces = $project->getIndexes()->get('interfaces', new Collection())->getAll();
        $traits = $project->getIndexes()->get('traits', new Collection())->getAll();

        /** @var ClassDescriptor[]|InterfaceDescriptor[]|TraitDescriptor[] $containers */
        $containers = array_merge($classes, $interfaces, $traits);

        foreach ($containers as $container) {
            $from_name = (string) $container->getFullyQualifiedStructuralElementName();

            $parents = [];
            $implemented = [];
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
                if ($edge !== null) {
                    $edge->setArrowHead('empty');
                    $graph->link($edge);
                }
            }

            /** @var string|ClassDescriptor|InterfaceDescriptor $parent */
            foreach ($implemented as $parent) {
                $edge = $this->createEdge($graph, $from_name, $parent);
                if ($edge !== null) {
                    $edge->setStyle('dotted');
                    $edge->setArrowHead('empty');
                    $graph->link($edge);
                }
            }
        }

        $graph->export('svg', $filename);
    }

    /**
     * Creates a GraphViz Edge between two nodes.
     *
     * @param Graph  $graph
     * @param string $from_name
     * @param string|ClassDescriptor|InterfaceDescriptor|TraitDescriptor $to
     *
     * @return Edge|null
     */
    protected function createEdge($graph, $from_name, $to)
    {
        $to_name = (string) ($to instanceof DescriptorAbstract ? $to->getFullyQualifiedStructuralElementName() : $to);

        if (!isset($this->nodeCache[$from_name])) {
            $namespaceParts = explode('\\', $from_name);
            $this->nodeCache[$from_name] = $this->createEmptyNode(
                array_pop($namespaceParts),
                $this->createNamespaceGraph($from_name)
            );
        }

        if (!isset($this->nodeCache[$to_name])) {
            $namespaceParts = explode('\\', $to_name);
            $this->nodeCache[$to_name] = $this->createEmptyNode(
                array_pop($namespaceParts),
                $this->createNamespaceGraph($to_name)
            );
        }

        $fromNode = $this->nodeCache[$from_name];
        $toNode = $this->nodeCache[$to_name];
        if ($fromNode !== null && $toNode !== null) {
            return Edge::create($fromNode, $toNode);
        }

        return null;
    }

    protected function createNamespaceGraph($fqcn)
    {
        $namespaceParts = explode('\\', $fqcn);

        // push the classname off the stack
        array_pop($namespaceParts);

        $graph = null;
        $reassembledFqnn = '';
        foreach ($namespaceParts as $part) {
            if ($part === '\\' || $part === '') {
                $part = 'Global';
                $reassembledFqnn = 'Global';
            } else {
                $reassembledFqnn = $reassembledFqnn . '\\' . $part;
            }

            if (isset($this->namespaceCache[$part])) {
                $graph = $this->namespaceCache[$part];
            } else {
                $subgraph = $this->createGraphForNamespace($reassembledFqnn, $part);
                $graph->addGraph($subgraph);
                $graph = $subgraph;
            }
        }

        return $graph;
    }

    /**
     * @param string $name
     */
    protected function createEmptyNode(string $name, ?GraphVizGraph $graph) : ?Node
    {
        if ($graph === null) {
            return null;
        }

        $node = Node::create($name);
        $node->setFontColor('gray');
        $node->setLabel($name);
        $graph->setNode($node);

        return $node;
    }

    /**
     * Builds a tree of namespace subgraphs with their classes associated.
     */
    protected function buildNamespaceTree(GraphVizGraph $graph, NamespaceDescriptor $namespace)
    {
        $full_namespace_name = (string) $namespace->getFullyQualifiedStructuralElementName();
        if ($full_namespace_name === '\\') {
            $full_namespace_name = 'Global';
        }

        $label = $namespace->getName() === '\\' ? 'Global' : $namespace->getName();
        $sub_graph = $this->createGraphForNamespace($full_namespace_name, $label);
        $this->namespaceCache[$full_namespace_name] = $sub_graph;

        $elements = array_merge(
            $namespace->getClasses()->getAll(),
            $namespace->getInterfaces()->getAll(),
            $namespace->getTraits()->getAll()
        );

        /** @var ClassDescriptor|InterfaceDescriptor|TraitDescriptor $sub_element */
        foreach ($elements as $sub_element) {
            $node = Node::create(
                (string) $sub_element->getFullyQualifiedStructuralElementName(),
                $sub_element->getName()
            )
                ->setShape('box')
                ->setFontName($this->nodeFont)
                ->setFontSize('11');

            if ($sub_element instanceof ClassDescriptor && $sub_element->isAbstract()) {
                $node->setLabel('<«abstract»<br/>' . $sub_element->getName() . '>');
            }

            //$full_name = $sub_element->getFullyQualifiedStructuralElementName();
            //$node->setURL($this->class_paths[$full_name]);
            //$node->setTarget('_parent');

            $this->nodeCache[(string) $sub_element->getFullyQualifiedStructuralElementName()] = $node;
            $sub_graph->setNode($node);
        }

        foreach ($namespace->getChildren()->getAll() as $element) {
            $this->buildNamespaceTree($sub_graph, $element);
        }

        $graph->addGraph($sub_graph);
    }

    protected function getDestinationPath(Transformation $transformation)
    {
        return $transformation->getTransformer()->getTarget()
            . DIRECTORY_SEPARATOR . $transformation->getArtifact();
    }

    /**
     * Checks whether GraphViz is installed and throws an Exception otherwise.
     *
     * @throws ExtensionNotLoadedException if graphviz is not found.
     */
    protected function checkIfGraphVizIsInstalled()
    {
        // NOTE: the -V flag sends output using STDERR and STDOUT
        exec('dot -V 2>&1', $output, $error);
        if ($error !== 0) {
            throw new ExtensionNotLoadedException(
                'Unable to find the `dot` command of the GraphViz package. '
                . 'Is GraphViz correctly installed and present in your path?'
            );
        }
    }

    /**
     * @param string $full_namespace_name
     * @param string $label
     *
     * @return mixed
     */
    protected function createGraphForNamespace($full_namespace_name, $label)
    {
        return GraphVizGraph::create('cluster_' . $full_namespace_name)
            ->setLabel($label)
            ->setFontColor('gray')
            ->setFontSize('11')
            ->setRankDir('LR');
    }
}
