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

namespace phpDocumentor\Guides\Nodes;

use phpDocumentor\Guides\Nodes\Metadata\MetadataNode;

use function array_filter;
use function array_map;
use function array_merge;
use function count;
use function in_array;
use function is_string;

final class DocumentNode extends Node
{
    /** @var string */
    private $hash;

    /**
     * Header nodes are rendered in the head of a html page.
     * They contain metadata about the document.
     *
     * @var MetadataNode[]
     */
    private $headerNodes = [];

    /** @var Node[] */
    private $nodes = [];

    /** @var string[] */
    private $dependencies = [];

    /** @var array<string|SpanNode> */
    private $variables = [];

    public function __construct(string $value)
    {
        parent::__construct();

        $this->hash = $value;
    }

    /**
     * @return Node[]
     */
    public function getNodes(?callable $function = null): array
    {
        if ($function === null) {
            return $this->nodes;
        }

        return array_filter($this->nodes, $function);
    }

    public function getTitle(): ?TitleNode
    {
        foreach ($this->nodes as $node) {
            if ($node instanceof TitleNode && $node->getLevel() === 1) {
                return $node;
            }
        }

        return null;
    }

    /**
     * @return TocNode[]
     */
    public function getTocs(): array
    {
        return $this->getNodes(
            static function ($node) {
                return $node instanceof TocNode;
            }
        );
    }

    /**
     * @return string[][]
     */
    public function getTitles(): array
    {
        $titles = [];
        $levels = [&$titles];

        foreach ($this->nodes as $node) {
            if (!($node instanceof TitleNode)) {
                continue;
            }

            $level = $node->getLevel();
            $text = $node->getValue()->getValue();
            $redirection = $node->getTarget();
            $value = $redirection !== '' ? [$text, $redirection] : $text;

            if (!isset($levels[$level - 1])) {
                continue;
            }

            $parent = &$levels[$level - 1];
            $element = [$value, []];
            $parent[] = $element;
            $levels[$level] = &$parent[count($parent) - 1][1];
        }

        $subDocumentTitles = array_map(
            static function (DocumentNode $node) {
                return $node->getTitles();
            },
            $this->getNodes(static function ($node) {
                return $node instanceof DocumentNode;
            })
        );

        return array_merge($titles, ...$subDocumentTitles);
    }

    /**
     * @param string|Node $node
     */
    public function addNode($node): void
    {
        if (is_string($node)) {
            $node = new RawNode($node);
        }

        if (!($node instanceof Node)) {
            return;
        }

        $this->nodes[] = $node;
    }

    public function addHeaderNode(MetadataNode $node): void
    {
        $this->headerNodes[] = $node;
    }

    /** @return MetadataNode[] */
    public function getHeaderNodes(): array
    {
        return $this->headerNodes;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function addDependency(string $dependencyName): void
    {
        if (in_array($dependencyName, $this->dependencies, true)) {
            return;
        }

        $this->dependencies[] = $dependencyName;
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @return array<string|SpanNode>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @param array<string|SpanNode> $variables
     */
    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }
}
