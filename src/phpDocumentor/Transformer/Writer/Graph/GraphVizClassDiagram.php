<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer\Graph;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\GraphViz\Edge;
use phpDocumentor\GraphViz\Graph as GraphVizGraph;
use phpDocumentor\GraphViz\Node;
use RuntimeException;
use Throwable;

use function array_merge;
use function array_pop;
use function exec;
use function explode;

final class GraphVizClassDiagram implements Generator
{
    /** @var array<string, ?Node> a cache where nodes for classes, interfaces and traits are stored for reference */
    private $nodeCache = [];

    /** @var GraphVizGraph[] */
    private $namespaceCache = [];

    /**
     * Creates a class inheritance diagram.
     */
    public function create(ProjectDescriptor $project, string $filename): void
    {
        try {
            $this->checkIfGraphVizIsInstalled();
        } catch (Throwable $e) {
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

        $classes = $project->getIndexes()->fetch('classes', new Collection())->getAll();
        $interfaces = $project->getIndexes()->fetch('interfaces', new Collection())->getAll();
        $traits = $project->getIndexes()->fetch('traits', new Collection())->getAll();

        /** @var ClassDescriptor[]|InterfaceDescriptor[]|TraitDescriptor[] $containers */
        $containers = array_merge($classes, $interfaces, $traits);

        foreach ($containers as $container) {
            $fromName = (string) $container->getFullyQualifiedStructuralElementName();

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
                $edge = $this->createEdge($fromName, $parent);
                if ($edge === null) {
                    continue;
                }

                $edge->setArrowHead('empty');
                $graph->link($edge);
            }

            /** @var string|ClassDescriptor|InterfaceDescriptor $parent */
            foreach ($implemented as $parent) {
                $edge = $this->createEdge($fromName, $parent);
                if ($edge === null) {
                    continue;
                }

                $edge->setStyle('dotted');
                $edge->setArrowHead('empty');
                $graph->link($edge);
            }
        }

        $graph->export('svg', $filename);
    }

    /**
     * Checks whether GraphViz is installed and throws an Exception otherwise.
     *
     * @throws RuntimeException If graphviz is not found.
     */
    private function checkIfGraphVizIsInstalled(): void
    {
        // NOTE: the -V flag sends output using STDERR and STDOUT
        exec('dot -V 2>&1', $output, $error);
        if ($error !== 0) {
            throw new RuntimeException(
                'Unable to find the `dot` command of the GraphViz package. '
                . 'Is GraphViz correctly installed and present in your path?'
            );
        }
    }

    /**
     * Creates a GraphViz Edge between two nodes.
     *
     * @param string|ClassDescriptor|InterfaceDescriptor|TraitDescriptor $to
     */
    private function createEdge(string $fromName, $to): ?Edge
    {
        $toName = (string) ($to instanceof DescriptorAbstract ? $to->getFullyQualifiedStructuralElementName() : $to);

        if (!isset($this->nodeCache[$fromName])) {
            $namespaceParts = explode('\\', $fromName);
            $this->nodeCache[$fromName] = $this->createEmptyNode(
                array_pop($namespaceParts),
                $this->createNamespaceGraph($fromName)
            );
        }

        if (!isset($this->nodeCache[$toName])) {
            $namespaceParts = explode('\\', $toName);
            $this->nodeCache[$toName] = $this->createEmptyNode(
                array_pop($namespaceParts),
                $this->createNamespaceGraph($toName)
            );
        }

        $fromNode = $this->nodeCache[$fromName];
        $toNode = $this->nodeCache[$toName];
        if ($fromNode !== null && $toNode !== null) {
            return Edge::create($fromNode, $toNode);
        }

        return null;
    }

    private function createNamespaceGraph(string $fqcn): ?GraphVizGraph
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
                $reassembledFqnn .= '\\' . $part;
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

    private function createEmptyNode(string $name, ?GraphVizGraph $graph): ?Node
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
    private function buildNamespaceTree(GraphVizGraph $graph, NamespaceInterface $namespace): void
    {
        $fullNamespaceName = (string) $namespace->getFullyQualifiedStructuralElementName();
        if ($fullNamespaceName === '\\') {
            $fullNamespaceName = 'Global';
        }

        $label = $namespace->getName() === '\\' ? 'Global' : $namespace->getName();
        $subGraph = $this->createGraphForNamespace($fullNamespaceName, $label);
        $this->namespaceCache[$fullNamespaceName] = $subGraph;

        $elements = array_merge(
            $namespace->getClasses()->getAll(),
            $namespace->getInterfaces()->getAll(),
            $namespace->getTraits()->getAll()
        );

        /** @var ClassDescriptor|InterfaceDescriptor|TraitDescriptor $subElement */
        foreach ($elements as $subElement) {
            $node = Node::create(
                (string) $subElement->getFullyQualifiedStructuralElementName(),
                $subElement->getName()
            )
                ->setShape('box')
                ->setFontName('Courier')
                ->setFontSize('11');

            if ($subElement instanceof ClassDescriptor && $subElement->isAbstract()) {
                $node->setLabel('<«abstract»<br/>' . $subElement->getName() . '>');
            }

            //$full_name = $subElement->getFullyQualifiedStructuralElementName();
            //$node->setURL($this->class_paths[$full_name]);
            //$node->setTarget('_parent');

            $this->nodeCache[(string) $subElement->getFullyQualifiedStructuralElementName()] = $node;
            $subGraph->setNode($node);
        }

        foreach ($namespace->getChildren()->getAll() as $element) {
            $this->buildNamespaceTree($subGraph, $element);
        }

        $graph->addGraph($subGraph);
    }

    private function createGraphForNamespace(string $fullNamespaceName, string $label): GraphVizGraph
    {
        return GraphVizGraph::create('cluster_' . $fullNamespaceName)
            ->setLabel($label)
            ->setFontColor('gray')
            ->setFontSize('11')
            ->setRankDir('LR');
    }
}
