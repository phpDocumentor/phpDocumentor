<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\LaTeX;

use IteratorAggregate;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\NodeRenderers;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\Nodes;
use phpDocumentor\Guides\ReferenceRegistry;
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

    public function getNodeRendererFactory(ReferenceRegistry $referenceRegistry): NodeRendererFactory
    {
        return new NodeRenderers\InMemoryNodeRendererFactory(
            [
                Nodes\AnchorNode::class => new NodeRenderers\TemplateNodeRenderer($this->renderer, 'anchor.tex.twig'),
                Nodes\CodeNode::class => new NodeRenderers\TemplateNodeRenderer($this->renderer, 'code.tex.twig'),
                Nodes\ImageNode::class => new NodeRenderers\TemplateNodeRenderer($this->renderer, 'image.tex.twig'),
                Nodes\MetaNode::class => new NodeRenderers\TemplateNodeRenderer($this->renderer, 'meta.tex.twig'),
                Nodes\ParagraphNode::class => new NodeRenderers\TemplateNodeRenderer($this->renderer, 'paragraph.tex.twig'),
                Nodes\QuoteNode::class => new NodeRenderers\TemplateNodeRenderer($this->renderer, 'quote.tex.twig'),
                Nodes\SeparatorNode::class => new NodeRenderers\TemplateNodeRenderer($this->renderer, 'separator.tex.twig'),
                Nodes\ListNode::class => new NodeRenderers\TemplateNodeRenderer($this->renderer, 'list.tex.twig'),
                Nodes\TableNode::class => new NodeRenderers\LaTeX\TableNodeRenderer(),
                Nodes\TitleNode::class => new NodeRenderers\LaTeX\TitleNodeRenderer($this->renderer),
                Nodes\TocNode::class => new NodeRenderers\LaTeX\TocNodeRenderer($this->renderer, $referenceRegistry),
                Nodes\DocumentNode::class => new NodeRenderers\LaTeX\DocumentNodeRenderer(),
                Nodes\SpanNode::class => new NodeRenderers\LaTeX\SpanNodeRenderer($this->renderer, $referenceRegistry),
            ],
            new NodeRenderers\DefaultNodeRenderer()
        );
    }
}
