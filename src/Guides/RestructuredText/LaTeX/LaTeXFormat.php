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
    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(TemplateRenderer $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }

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
                function (Nodes\AnchorNode $node) {
                    return new Renderers\LaTeX\AnchorNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\CodeNode::class => new CallableNodeRendererFactory(
                function (Nodes\CodeNode $node) {
                    return new Renderers\LaTeX\CodeNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\ImageNode::class => new CallableNodeRendererFactory(
                function (Nodes\ImageNode $node) {
                    return new Renderers\LaTeX\ImageNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\ListNode::class => new CallableNodeRendererFactory(
                function (Nodes\ListNode $node) {
                    return new Renderers\ListNodeRenderer(
                        $node,
                        new Renderers\LaTeX\ListRenderer($node, $this->templateRenderer)
                    );
                }
            ),
            Nodes\MetaNode::class => new CallableNodeRendererFactory(
                function (Nodes\MetaNode $node) {
                    return new Renderers\LaTeX\MetaNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\ParagraphNode::class => new CallableNodeRendererFactory(
                function (Nodes\ParagraphNode $node) {
                    return new Renderers\LaTeX\ParagraphNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\QuoteNode::class => new CallableNodeRendererFactory(
                function (Nodes\QuoteNode $node) {
                    return new Renderers\LaTeX\QuoteNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\SeparatorNode::class => new CallableNodeRendererFactory(
                function (Nodes\SeparatorNode $node) {
                    return new Renderers\LaTeX\SeparatorNodeRenderer(
                        $this->templateRenderer
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
                function (Nodes\TitleNode $node) {
                    return new Renderers\LaTeX\TitleNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\TocNode::class => new CallableNodeRendererFactory(
                function (Nodes\TocNode $node) {
                    return new Renderers\LaTeX\TocNodeRenderer(
                        $node->getEnvironment(),
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\DocumentNode::class => new CallableNodeRendererFactory(
                function (Nodes\DocumentNode $node) {
                    return new Renderers\LaTeX\DocumentNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\SpanNode::class => new CallableNodeRendererFactory(
                function (Nodes\SpanNode $node) {
                    return new Renderers\LaTeX\SpanNodeRenderer(
                        $node->getEnvironment(),
                        $node,
                        $this->templateRenderer
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
