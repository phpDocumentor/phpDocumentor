<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\LaTeX;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\NodeRenderers;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\Nodes;
use phpDocumentor\Guides\RestructuredText\Formats\Format;

class LaTeXFormat extends Format
{
    public function getNodeRendererFactory(Environment $environment): NodeRendererFactory
    {
        $renderer = $environment->getRenderer();

        return new NodeRenderers\InMemoryNodeRendererFactory(
            [
                Nodes\AnchorNode::class => new NodeRenderers\TemplateNodeRenderer($renderer, 'anchor.tex.twig'),
                Nodes\CodeNode::class => new NodeRenderers\TemplateNodeRenderer($renderer, 'code.tex.twig'),
                Nodes\ImageNode::class => new NodeRenderers\TemplateNodeRenderer($renderer, 'image.tex.twig'),
                Nodes\MetaNode::class => new NodeRenderers\TemplateNodeRenderer($renderer, 'meta.tex.twig'),
                Nodes\ParagraphNode::class => new NodeRenderers\TemplateNodeRenderer($renderer, 'paragraph.tex.twig'),
                Nodes\QuoteNode::class => new NodeRenderers\TemplateNodeRenderer($renderer, 'quote.tex.twig'),
                Nodes\SeparatorNode::class => new NodeRenderers\TemplateNodeRenderer($renderer, 'separator.tex.twig'),
                Nodes\ListNode::class => new NodeRenderers\ListNodeRenderer(
                    new NodeRenderers\LaTeX\ListRenderer($renderer),
                    $environment
                ),
                Nodes\TableNode::class => new NodeRenderers\LaTeX\TableNodeRenderer(
                    $environment->getNodeRendererFactory()
                ),
                Nodes\TitleNode::class => new NodeRenderers\LaTeX\TitleNodeRenderer($renderer),
                Nodes\TocNode::class => new NodeRenderers\LaTeX\TocNodeRenderer($environment),
                Nodes\DocumentNode::class => new NodeRenderers\LaTeX\DocumentNodeRenderer($environment),
                Nodes\SpanNode::class => new NodeRenderers\LaTeX\SpanNodeRenderer($environment),
            ],
            new NodeRenderers\DefaultNodeRenderer($environment)
        );
    }
}
