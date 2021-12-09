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

namespace phpDocumentor\Guides\NodeRenderers\Html;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\NodeRenderers\DocumentNodeRenderer as BaseDocumentRender;
use phpDocumentor\Guides\NodeRenderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactoryAware;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\Node;
use Webmozart\Assert\Assert;

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
        Assert::isInstanceOf($node, DocumentNode::class);

        return (new BaseDocumentRender($this->nodeRendererFactory))->render($node, $environment);
    }

    public function renderDocument(DocumentNode $node, Environment $environment): string
    {
        $renderer = $environment->getRenderer();
        $renderer->setGuidesEnvironment($environment);

        return $renderer->render(
            'document.html.twig',
            ['node' => $node]
        );
    }

    public function supports(Node $node): bool
    {
        return $node instanceof DocumentNode;
    }
}
