<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\LaTeX;

use phpDocumentor\Guides\Nodes;
use phpDocumentor\Guides\Renderers;
use phpDocumentor\Guides\Renderers\CallableNodeRendererFactory;
use phpDocumentor\Guides\Renderers\NodeRendererFactory;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Formats\Format;
use phpDocumentor\Guides\RestructuredText\LaTeX;
use phpDocumentor\Guides\TemplateRenderer;

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
            new LaTeX\Directives\Stylesheet(),
            new LaTeX\Directives\Title(),
            new LaTeX\Directives\Url(),
            new LaTeX\Directives\Wrap('note'),
        ];
    }

    /**
     * @return NodeRendererFactory[]
     */
    public function getNodeRendererFactories(TemplateRenderer $templateRenderer) : array
    {
        return [
            Nodes\AnchorNode::class => new CallableNodeRendererFactory(
                function (Nodes\AnchorNode $node) use ($templateRenderer) {
                    return new Renderers\LaTeX\AnchorNodeRenderer(
                        $node,
                        $templateRenderer
                    );
                }
            ),
            Nodes\CodeNode::class => new CallableNodeRendererFactory(
                function (Nodes\CodeNode $node) use ($templateRenderer) {
                    return new Renderers\LaTeX\CodeNodeRenderer(
                        $node,
                        $templateRenderer
                    );
                }
            ),
            Nodes\ImageNode::class => new CallableNodeRendererFactory(
                function (Nodes\ImageNode $node) use ($templateRenderer) {
                    return new Renderers\LaTeX\ImageNodeRenderer(
                        $node,
                        $templateRenderer
                    );
                }
            ),
            Nodes\ListNode::class => new CallableNodeRendererFactory(
                function (Nodes\ListNode $node) use ($templateRenderer) {
                    return new Renderers\ListNodeRenderer(
                        $node,
                        new Renderers\LaTeX\ListRenderer($node, $templateRenderer)
                    );
                }
            ),
            Nodes\MetaNode::class => new CallableNodeRendererFactory(
                function (Nodes\MetaNode $node) use ($templateRenderer) {
                    return new Renderers\LaTeX\MetaNodeRenderer(
                        $node,
                        $templateRenderer
                    );
                }
            ),
            Nodes\ParagraphNode::class => new CallableNodeRendererFactory(
                function (Nodes\ParagraphNode $node) use ($templateRenderer) {
                    return new Renderers\LaTeX\ParagraphNodeRenderer(
                        $node,
                        $templateRenderer
                    );
                }
            ),
            Nodes\QuoteNode::class => new CallableNodeRendererFactory(
                function (Nodes\QuoteNode $node) use ($templateRenderer) {
                    return new Renderers\LaTeX\QuoteNodeRenderer(
                        $node,
                        $templateRenderer
                    );
                }
            ),
            Nodes\SeparatorNode::class => new CallableNodeRendererFactory(
                function (Nodes\SeparatorNode $node) use ($templateRenderer) {
                    return new Renderers\LaTeX\SeparatorNodeRenderer(
                        $templateRenderer
                    );
                }
            ),
            Nodes\TableNode::class => new CallableNodeRendererFactory(
                static function (Nodes\TableNode $node) {
                    return new Renderers\LaTeX\TableNodeRenderer(
                        $node
                    );
                }
            ),
            Nodes\TitleNode::class => new CallableNodeRendererFactory(
                function (Nodes\TitleNode $node) use ($templateRenderer) {
                    return new Renderers\LaTeX\TitleNodeRenderer(
                        $node,
                        $templateRenderer
                    );
                }
            ),
            Nodes\TocNode::class => new CallableNodeRendererFactory(
                function (Nodes\TocNode $node) use ($templateRenderer) {
                    return new Renderers\LaTeX\TocNodeRenderer(
                        $node->getEnvironment(),
                        $node,
                        $templateRenderer
                    );
                }
            ),
            Nodes\DocumentNode::class => new CallableNodeRendererFactory(
                function (Nodes\DocumentNode $node) use ($templateRenderer) {
                    return new Renderers\LaTeX\DocumentNodeRenderer(
                        $node,
                        $templateRenderer
                    );
                }
            ),
            Nodes\SpanNode::class => new CallableNodeRendererFactory(
                function (Nodes\SpanNode $node) use ($templateRenderer) {
                    return new Renderers\LaTeX\SpanNodeRenderer(
                        $node->getEnvironment(),
                        $node,
                        $templateRenderer
                    );
                }
            ),
            Nodes\CallableNode::class => new CallableNodeRendererFactory(
                static function (Nodes\CallableNode $node) {
                    return new Renderers\CallableNodeRenderer(
                        $node
                    );
                }
            ),
        ];
    }
}
