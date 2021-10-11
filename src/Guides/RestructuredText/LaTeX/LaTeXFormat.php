<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\LaTeX;

use IteratorAggregate;
use phpDocumentor\Guides\NodeRenderers;
use phpDocumentor\Guides\NodeRenderers\DefaultNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\LaTeX\DocumentNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\LaTeX\SpanNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\LaTeX\TableNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\LaTeX\TitleNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\LaTeX\TocNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\NodeRenderers\TemplateNodeRenderer;
use phpDocumentor\Guides\Nodes;
use phpDocumentor\Guides\ReferenceBuilder;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\RestructuredText\Formats\Format;

class LaTeXFormat extends Format
{
    /** @var Renderer */
    private $renderer;

    public function __construct(string $fileExtension, IteratorAggregate $directives, Renderer $renderer)
    {
        $this->renderer = $renderer;

        parent::__construct($fileExtension, $directives);
    }

    public function getNodeRendererFactory(ReferenceBuilder $referenceRegistry): NodeRendererFactory
    {
        return new NodeRenderers\InMemoryNodeRendererFactory(
            [
                Nodes\AnchorNode::class => new TemplateNodeRenderer($this->renderer, 'anchor.tex.twig'),
                Nodes\CodeNode::class => new TemplateNodeRenderer($this->renderer, 'code.tex.twig'),
                Nodes\ImageNode::class => new TemplateNodeRenderer($this->renderer, 'image.tex.twig'),
                Nodes\MetaNode::class => new TemplateNodeRenderer($this->renderer, 'meta.tex.twig'),
                Nodes\ParagraphNode::class => new TemplateNodeRenderer($this->renderer, 'paragraph.tex.twig'),
                Nodes\QuoteNode::class => new TemplateNodeRenderer($this->renderer, 'quote.tex.twig'),
                Nodes\SeparatorNode::class => new TemplateNodeRenderer($this->renderer, 'separator.tex.twig'),
                Nodes\ListNode::class => new TemplateNodeRenderer($this->renderer, 'list.tex.twig'),
                Nodes\TableNode::class => new TableNodeRenderer(),
                Nodes\TitleNode::class => new TitleNodeRenderer($this->renderer),
                Nodes\TocNode::class => new TocNodeRenderer($this->renderer, $referenceRegistry),
                Nodes\DocumentNode::class => new DocumentNodeRenderer(),
                Nodes\SpanNode::class => new SpanNodeRenderer($this->renderer, $referenceRegistry),
            ],
            new DefaultNodeRenderer()
        );
    }
}
