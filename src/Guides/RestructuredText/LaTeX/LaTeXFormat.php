<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\LaTeX;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes;
use phpDocumentor\Guides\Renderers;
use phpDocumentor\Guides\Renderers\CallableNodeRendererFactory;
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

    /**
     * @return NodeRendererFactory[]
     */
    public function getNodeRendererFactory(Environment $environment) : NodeRendererFactory
    {
        $renderer = $environment->getRenderer();

        return new Renderers\InMemoryNodeRendererFactory(
            [
                Nodes\AnchorNode::class => new Renderers\LaTeX\AnchorNodeRenderer($renderer),
                Nodes\CodeNode::class => new Renderers\LaTeX\CodeNodeRenderer($renderer),
                Nodes\ImageNode::class => new Renderers\LaTeX\ImageNodeRenderer($renderer),
                Nodes\ListNode::class => new Renderers\ListNodeRenderer(new Renderers\LaTeX\ListRenderer($renderer)),
                Nodes\MetaNode::class => new Renderers\LaTeX\MetaNodeRenderer($renderer),
                Nodes\ParagraphNode::class => new Renderers\LaTeX\ParagraphNodeRenderer($renderer),
                Nodes\QuoteNode::class => new Renderers\LaTeX\QuoteNodeRenderer($renderer),
                Nodes\SeparatorNode::class => new Renderers\LaTeX\SeparatorNodeRenderer($renderer),
                Nodes\TableNode::class => new Renderers\LaTeX\TableNodeRenderer(),
                Nodes\TitleNode::class => new Renderers\LaTeX\TitleNodeRenderer($renderer),
                Nodes\TocNode::class => new Renderers\LaTeX\TocNodeRenderer($environment),
                Nodes\DocumentNode::class => new Renderers\LaTeX\DocumentNodeRenderer($renderer),
                Nodes\SpanNode::class => new Renderers\LaTeX\SpanNodeRenderer($environment),
                Nodes\CallableNode::class => new Renderers\CallableNodeRenderer(),
            ],
            new Renderers\DefaultNodeRenderer()
        );
    }
}
