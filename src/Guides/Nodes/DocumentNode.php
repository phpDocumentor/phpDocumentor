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

use phpDocumentor\Guides\Environment;
use function array_unshift;
use function count;
use function get_class;
use function is_string;

class DocumentNode extends Node
{
    /** @var Environment */
    protected $environment;

    /** @var Node[] */
    protected $headerNodes = [];

    /** @var Node[] */
    protected $nodes = [];

    public function __construct(Environment $environment)
    {
        parent::__construct();

        $this->environment = $environment;
    }

    /**
     * @return Node[]
     */
    public function getHeaderNodes() : array
    {
        return $this->headerNodes;
    }

    /**
     * @return Node[]
     */
    public function getNodes(?callable $function = null) : array
    {
        $nodes = [];

        if ($function === null) {
            return $this->nodes;
        }

        foreach ($this->nodes as $node) {
            if (!$function($node)) {
                continue;
            }

            $nodes[] = $node;
        }

        return $nodes;
    }

    public function getTitle() : ?string
    {
        foreach ($this->nodes as $node) {
            if ($node instanceof TitleNode && $node->getLevel() === 1) {
                return $this->environment->getNodeRendererFactory()
                    ->get(get_class($node->getValue()))
                    ->render($node->getValue());
            }
        }

        return null;
    }

    /**
     * @return mixed[]
     */
    public function getTocs() : array
    {
        $tocs = [];

        $nodes = $this->getNodes(
            static function ($node) {
                return $node instanceof TocNode;
            }
        );

        /** @var TocNode $toc */
        foreach ($nodes as $toc) {
            $files = $toc->getFiles();

            foreach ($files as &$file) {
                $file = $this->environment->canonicalUrl($file);
            }

            $tocs[] = $files;
        }

        return $tocs;
    }

    /**
     * @return string[][]
     */
    public function getTitles() : array
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

        return $titles;
    }

    /**
     * @param string|Node $node
     */
    public function addNode($node) : void
    {
        if (is_string($node)) {
            $node = new RawNode($node);
        }

        $this->nodes[] = $node;
    }

    public function prependNode(Node $node) : void
    {
        array_unshift($this->nodes, $node);
    }

    public function addHeaderNode(Node $node) : void
    {
        $this->headerNodes[] = $node;
    }
}
