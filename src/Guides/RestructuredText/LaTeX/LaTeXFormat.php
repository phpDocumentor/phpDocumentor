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
    public function getNodeRendererFactories() : array
    {
        return [
            Nodes\AnchorNode::class => new CallableNodeRendererFactory(
                static function (Nodes\AnchorNode $node) {
                    return new Renderers\LaTeX\AnchorNodeRenderer($node);
                }
            ),
            Nodes\CodeNode::class => new CallableNodeRendererFactory(
                static function (Nodes\CodeNode $node) {
                    return new Renderers\LaTeX\CodeNodeRenderer($node);
                }
            ),
            Nodes\ImageNode::class => new CallableNodeRendererFactory(
                static function (Nodes\ImageNode $node) {
                    return new Renderers\LaTeX\ImageNodeRenderer($node);
                }
            ),
            Nodes\ListNode::class => new CallableNodeRendererFactory(
                static function (Nodes\ListNode $node) {
                    return new Renderers\ListNodeRenderer(
                        $node,
                        new Renderers\LaTeX\ListRenderer($node)
                    );
                }
            ),
            Nodes\MetaNode::class => new CallableNodeRendererFactory(
                static function (Nodes\MetaNode $node) {
                    return new Renderers\LaTeX\MetaNodeRenderer($node);
                }
            ),
            Nodes\ParagraphNode::class => new CallableNodeRendererFactory(
                static function (Nodes\ParagraphNode $node) {
                    return new Renderers\LaTeX\ParagraphNodeRenderer($node);
                }
            ),
            Nodes\QuoteNode::class => new CallableNodeRendererFactory(
                static function (Nodes\QuoteNode $node) {
                    return new Renderers\LaTeX\QuoteNodeRenderer($node);
                }
            ),
            Nodes\SeparatorNode::class => new CallableNodeRendererFactory(
                static function (Nodes\SeparatorNode $node) {
                    return new Renderers\LaTeX\SeparatorNodeRenderer($node);
                }
            ),
            Nodes\TableNode::class => new CallableNodeRendererFactory(
                static function (Nodes\TableNode $node) {
                    return new Renderers\LaTeX\TableNodeRenderer($node);
                }
            ),
            Nodes\TitleNode::class => new CallableNodeRendererFactory(
                static function (Nodes\TitleNode $node) {
                    return new Renderers\LaTeX\TitleNodeRenderer($node);
                }
            ),
            Nodes\TocNode::class => new CallableNodeRendererFactory(
                static function (Nodes\TocNode $node) {
                    return new Renderers\LaTeX\TocNodeRenderer($node);
                }
            ),
            Nodes\DocumentNode::class => new CallableNodeRendererFactory(
                static function (Nodes\DocumentNode $node) {
                    return new Renderers\LaTeX\DocumentNodeRenderer($node);
                }
            ),
            Nodes\SpanNode::class => new CallableNodeRendererFactory(
                static function (Nodes\SpanNode $node) {
                    return new Renderers\LaTeX\SpanNodeRenderer($node);
                }
            ),
            Nodes\CallableNode::class => new CallableNodeRendererFactory(
                static function (Nodes\CallableNode $node) {
                    return new Renderers\CallableNodeRenderer($node);
                }
            ),
        ];
    }
}
