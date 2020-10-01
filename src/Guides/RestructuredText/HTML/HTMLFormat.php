<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML;

use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Formats\Format;
use phpDocumentor\Guides\RestructuredText\HTML;
use phpDocumentor\Guides\RestructuredText\Nodes;
use phpDocumentor\Guides\RestructuredText\Renderers;
use phpDocumentor\Guides\RestructuredText\Renderers\CallableNodeRendererFactory;
use phpDocumentor\Guides\RestructuredText\Renderers\NodeRendererFactory;
use phpDocumentor\Guides\RestructuredText\Templates\TemplateRenderer;

class HTMLFormat implements Format
{
    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(TemplateRenderer $templateRenderer)
    {
        $this->templateRenderer = $templateRenderer;
    }

    public function getFileExtension() : string
    {
        return Format::HTML;
    }

    /**
     * @return Directive[]
     */
    public function getDirectives() : array
    {
        return [
            new HTML\Directives\Image(),
            new HTML\Directives\Figure(),
            new HTML\Directives\Meta(),
            new HTML\Directives\Stylesheet(),
            new HTML\Directives\Title(),
            new HTML\Directives\Url(),
            new HTML\Directives\Div(),
            new HTML\Directives\Wrap('note'),
            new HTML\Directives\ClassDirective(),
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
                    return new HTML\Renderers\AnchorNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\CodeNode::class => new CallableNodeRendererFactory(
                function (Nodes\CodeNode $node) {
                    return new HTML\Renderers\CodeNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\DefinitionListNode::class => new CallableNodeRendererFactory(
                function (Nodes\DefinitionListNode $node) {
                    return new HTML\Renderers\DefinitionListNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\FigureNode::class => new CallableNodeRendererFactory(
                function (Nodes\FigureNode $node) {
                    return new HTML\Renderers\FigureNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\ImageNode::class => new CallableNodeRendererFactory(
                function (Nodes\ImageNode $node) {
                    return new HTML\Renderers\ImageNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\ListNode::class => new CallableNodeRendererFactory(
                function (Nodes\ListNode $node) {
                    return new Renderers\ListNodeRenderer(
                        $node,
                        new HTML\Renderers\ListRenderer($node, $this->templateRenderer)
                    );
                }
            ),
            Nodes\MetaNode::class => new CallableNodeRendererFactory(
                function (Nodes\MetaNode $node) {
                    return new HTML\Renderers\MetaNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\ParagraphNode::class => new CallableNodeRendererFactory(
                function (Nodes\ParagraphNode $node) {
                    return new HTML\Renderers\ParagraphNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\QuoteNode::class => new CallableNodeRendererFactory(
                function (Nodes\QuoteNode $node) {
                    return new HTML\Renderers\QuoteNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\SeparatorNode::class => new CallableNodeRendererFactory(
                function (Nodes\SeparatorNode $node) {
                    return new HTML\Renderers\SeparatorNodeRenderer(
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\TableNode::class => new CallableNodeRendererFactory(
                function (Nodes\TableNode $node) {
                    return new HTML\Renderers\TableNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\TitleNode::class => new CallableNodeRendererFactory(
                function (Nodes\TitleNode $node) {
                    return new HTML\Renderers\TitleNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\TocNode::class => new CallableNodeRendererFactory(
                function (Nodes\TocNode $node) {
                    return new HTML\Renderers\TocNodeRenderer(
                        $node->getEnvironment(),
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\DocumentNode::class => new CallableNodeRendererFactory(
                function (Nodes\DocumentNode $node) {
                    return new HTML\Renderers\DocumentNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\SpanNode::class => new CallableNodeRendererFactory(
                function (Nodes\SpanNode $node) {
                    return new HTML\Renderers\SpanNodeRenderer(
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
            Nodes\SectionBeginNode::class => new CallableNodeRendererFactory(
                function (Nodes\SectionBeginNode $node) {
                    return new HTML\Renderers\SectionBeginNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            Nodes\SectionEndNode::class => new CallableNodeRendererFactory(
                function (Nodes\SectionEndNode $node) {
                    return new HTML\Renderers\SectionEndNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
        ];
    }
}
