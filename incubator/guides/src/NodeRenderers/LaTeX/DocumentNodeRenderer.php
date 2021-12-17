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
use phpDocumentor\Guides\NodeRenderers\DocumentNodeRenderer as BaseDocumentRender;
use phpDocumentor\Guides\NodeRenderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactoryAware;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\MainNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Renderer;

use function count;

class DocumentNodeRenderer implements NodeRenderer, FullDocumentNodeRenderer, NodeRendererFactoryAware
{
    /** @var NodeRendererFactory */
    private $nodeRendererFactory;

    /** @var Renderer */
    private $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function setNodeRendererFactory(NodeRendererFactory $nodeRendererFactory): void
    {
        $this->nodeRendererFactory = $nodeRendererFactory;
    }

    public function render(Node $node, RenderContext $environment): string
    {
        if ($node instanceof DocumentNode === false) {
            throw new InvalidArgumentException('Invalid node presented');
        }

        return (new BaseDocumentRender($this->nodeRendererFactory))->render($node, $environment);
    }

    public function renderDocument(DocumentNode $node, RenderContext $environment): string
    {
        $this->renderer->setGuidesEnvironment($environment);

        return $this->renderer->render(
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
