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
use phpDocumentor\Guides\RestructuredText\OutputFormat;

class LaTeXFormat extends OutputFormat
{
    /** @var NodeRendererFactory */
    private $nodeRendererFactory;

    public function __construct(
        Renderer $renderer,
        ReferenceBuilder $referenceBuilder,
        string $fileExtension,
        IteratorAggregate $directives
    ) {
        parent::__construct($fileExtension, $directives);

        $this->nodeRendererFactory = new NodeRenderers\InMemoryNodeRendererFactory(
            [
                Nodes\AnchorNode::class => new TemplateNodeRenderer($renderer, 'anchor.tex.twig'),
                Nodes\CodeNode::class => new TemplateNodeRenderer($renderer, 'code.tex.twig'),
                Nodes\ImageNode::class => new TemplateNodeRenderer($renderer, 'image.tex.twig'),
                Nodes\MetaNode::class => new TemplateNodeRenderer($renderer, 'meta.tex.twig'),
                Nodes\ParagraphNode::class => new TemplateNodeRenderer($renderer, 'paragraph.tex.twig'),
                Nodes\QuoteNode::class => new TemplateNodeRenderer($renderer, 'quote.tex.twig'),
                Nodes\SeparatorNode::class => new TemplateNodeRenderer($renderer, 'separator.tex.twig'),
                Nodes\ListNode::class => new TemplateNodeRenderer($renderer, 'list.tex.twig'),
                Nodes\TableNode::class => new TableNodeRenderer(),
                Nodes\TitleNode::class => new TitleNodeRenderer($renderer),
                Nodes\TocNode::class => new TocNodeRenderer($renderer, $referenceBuilder),
                Nodes\DocumentNode::class => new DocumentNodeRenderer(),
                Nodes\SpanNode::class => new SpanNodeRenderer($renderer, $referenceBuilder),
            ],
            new DefaultNodeRenderer()
        );
    }

    public function getNodeRendererFactory(): NodeRendererFactory
    {
        return $this->nodeRendererFactory;
    }
}
