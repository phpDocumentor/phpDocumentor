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
use function strtolower;
use function trim;

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

    /**
     * Variables are replacements in a document.
     *
     * They easiest example is the replace directive that allows textual replacements in the document. But
     * also other directives may be prefixed with a name to replace a certain value in the text.
     *
     * @var array<string|Node>
     */
    private $variables = [];

    /** @var string Absolute file path of this document */
    private string $filePath;

    /** @var string[] */
    private array $links;

    public function __construct(string $value, string $filePath)
    {
        parent::__construct();

        $this->hash = $value;
        $this->filePath = $filePath;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
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
     * @param mixed $default
     *
     * @return string|Node
     */
    public function getVariable(string $name, $default)
    {
        return $this->variables[$name] ?? $default;
    }

    /** @param string|Node $value */
    public function addVariable(string $name, $value): void
    {
        $this->variables[$name] = $value;
    }

    /** @param array<string, string> $links */
    public function setLinks(array $links): void
    {
        $this->links = $links;
    }

    public function getLink(string $name): ?string
    {
        return $this->links[strtolower(trim($name))] ?? null;
    }
}
