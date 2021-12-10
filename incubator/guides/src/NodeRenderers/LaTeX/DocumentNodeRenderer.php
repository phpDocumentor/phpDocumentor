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

namespace phpDocumentor\Guides\NodeRenderers\LaTeX;

use InvalidArgumentException;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\NodeRenderers\DocumentNodeRenderer as BaseDocumentRender;
use phpDocumentor\Guides\NodeRenderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactoryAware;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\MainNode;
use phpDocumentor\Guides\Nodes\Node;

use function count;

class DocumentNodeRenderer implements NodeRenderer, FullDocumentNodeRenderer, NodeRendererFactoryAware
{
    /** @var NodeRendererFactory */
    private $nodeRendererFactory;

    public function setNodeRendererFactory(NodeRendererFactory $nodeRendererFactory): void
    {
        $this->nodeRendererFactory = $nodeRendererFactory;
    }

    public function render(Node $node, Environment $environment): string
    {
        if ($node instanceof DocumentNode === false) {
            throw new InvalidArgumentException('Invalid node presented');
        }

        return (new BaseDocumentRender($this->nodeRendererFactory))->render($node, $environment);
    }

    public function renderDocument(DocumentNode $node, Environment $environment): string
    {
        $renderer = $environment->getRenderer();

        return $renderer->render(
            'document.tex.twig',
            [
                'isMain' => $this->isMain($node),
                'document' => $node,
                'body' => $this->render($node, $environment),
            ]
        );
    }

    private function isMain(DocumentNode $node): bool
    {
        $nodes = $node->getNodes(
            static function ($node) {
                return $node instanceof MainNode;
            }
        );

        return count($nodes) !== 0;
    }

    public function supports(Node $node): bool
    {
        return $node instanceof DocumentNode;
    }
}
