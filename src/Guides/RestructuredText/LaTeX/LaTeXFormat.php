<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\LaTeX;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes;
use phpDocumentor\Guides\Renderers;
use phpDocumentor\Guides\Renderers\NodeRendererFactory;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Formats\Format;
use phpDocumentor\Guides\RestructuredText\LaTeX;

class LaTeXFormat implements Format
{
    public function getFileExtension() : string
    {
        return Format::LATEX;
    }

    /**
     * @return Directive[]
     */
    public function getDirectives() : array
    {
        return [
            new LaTeX\Directives\LaTeXMain(),
            new LaTeX\Directives\Image(),
            new LaTeX\Directives\Meta(),
            new LaTeX\Directives\Title(),
            new LaTeX\Directives\Url(),
            new LaTeX\Directives\Wrap('note'),
        ];
    }

    public function getNodeRendererFactory(Environment $environment) : NodeRendererFactory
    {
        $renderer = $environment->getRenderer();

        return new Renderers\InMemoryNodeRendererFactory(
            [
                Nodes\AnchorNode::class => new Renderers\TemplateNodeRenderer($renderer, 'anchor.tex.twig'),
                Nodes\CodeNode::class => new Renderers\TemplateNodeRenderer($renderer, 'code.tex.twig'),
                Nodes\ImageNode::class => new Renderers\TemplateNodeRenderer($renderer, 'image.tex.twig'),
                Nodes\MetaNode::class => new Renderers\TemplateNodeRenderer($renderer, 'meta.tex.twig'),
                Nodes\ParagraphNode::class => new Renderers\TemplateNodeRenderer($renderer, 'paragraph.tex.twig'),
                Nodes\QuoteNode::class => new Renderers\TemplateNodeRenderer($renderer, 'quote.tex.twig'),
                Nodes\SeparatorNode::class => new Renderers\TemplateNodeRenderer($renderer, 'separator.tex.twig'),
                Nodes\ListNode::class => new Renderers\ListNodeRenderer(
                    new Renderers\LaTeX\ListRenderer($renderer),
                    $environment
                ),
                Nodes\TableNode::class => new Renderers\LaTeX\TableNodeRenderer($environment->getNodeRendererFactory()),
                Nodes\TitleNode::class => new Renderers\LaTeX\TitleNodeRenderer($renderer),
                Nodes\TocNode::class => new Renderers\LaTeX\TocNodeRenderer($environment),
                Nodes\DocumentNode::class => new Renderers\LaTeX\DocumentNodeRenderer(
                    $environment,
                    $renderer
                ),
                Nodes\SpanNode::class => new Renderers\LaTeX\SpanNodeRenderer($environment),
                Nodes\CallableNode::class => new Renderers\CallableNodeRenderer(),
            ],
            new Renderers\DefaultNodeRenderer($environment)
        );
    }
}
