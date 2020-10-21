<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\RestructuredText\HTML;

use phpDocumentor\Guides\Nodes\AnchorNode;
use phpDocumentor\Guides\Nodes\CallableNode;
use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\DefinitionListNode;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\FigureNode;
use phpDocumentor\Guides\Nodes\ImageNode;
use phpDocumentor\Guides\Nodes\ListNode;
use phpDocumentor\Guides\Nodes\MetaNode;
use phpDocumentor\Guides\Nodes\ParagraphNode;
use phpDocumentor\Guides\Nodes\QuoteNode;
use phpDocumentor\Guides\Nodes\SectionBeginNode;
use phpDocumentor\Guides\Nodes\SectionEndNode;
use phpDocumentor\Guides\Nodes\SeparatorNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\Nodes\TableNode;
use phpDocumentor\Guides\Nodes\TitleNode;
use phpDocumentor\Guides\Nodes\TocNode;
use phpDocumentor\Guides\Renderers\CallableNodeRenderer;
use phpDocumentor\Guides\Renderers\CallableNodeRendererFactory;
use phpDocumentor\Guides\Renderers\Html\AnchorNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\CodeNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\DefinitionListNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\DocumentNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\FigureNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\ImageNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\ListRenderer;
use phpDocumentor\Guides\Renderers\Html\MetaNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\ParagraphNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\QuoteNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\SectionBeginNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\SectionEndNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\SeparatorNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\SpanNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\TableNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\TitleNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\TocNodeRenderer;
use phpDocumentor\Guides\Renderers\ListNodeRenderer;
use phpDocumentor\Guides\Renderers\NodeRendererFactory;
use phpDocumentor\Guides\RestructuredText;
use phpDocumentor\Guides\RestructuredText\Formats\Format;

final class HTMLFormat implements Format
{
    public function getFileExtension() : string
    {
        return Format::HTML;
    }

    public function getDirectives() : array
    {
        return [
            new RestructuredText\HTML\Directives\Image(),
            new RestructuredText\HTML\Directives\Figure(),
            new RestructuredText\HTML\Directives\Meta(),
            new RestructuredText\HTML\Directives\Stylesheet(),
            new RestructuredText\HTML\Directives\Title(),
            new RestructuredText\HTML\Directives\Url(),
            new RestructuredText\HTML\Directives\Div(),
            new RestructuredText\HTML\Directives\ClassDirective(),
            new RestructuredText\HTML\Directives\ImportantDirective(),
            new RestructuredText\HTML\Directives\NoteDirective(),
            new RestructuredText\HTML\Directives\WarningDirective(),
            new RestructuredText\HTML\Directives\HintDirective(),
            new RestructuredText\HTML\Directives\SidebarDirective(),
        ];
    }

    /**
     * @return NodeRendererFactory[]
     */
    public function getNodeRendererFactories() : array
    {
        $nodeRendererFactories = [
            AnchorNode::class => new CallableNodeRendererFactory(
                static function (AnchorNode $node) {
                    return new AnchorNodeRenderer($node);
                }
            ),
            DefinitionListNode::class => new CallableNodeRendererFactory(
                static function (DefinitionListNode $node) {
                    return new DefinitionListNodeRenderer($node);
                }
            ),
            FigureNode::class => new CallableNodeRendererFactory(
                static function (FigureNode $node) {
                    return new FigureNodeRenderer($node);
                }
            ),
            ImageNode::class => new CallableNodeRendererFactory(
                static function (ImageNode $node) {
                    return new ImageNodeRenderer($node);
                }
            ),
            ListNode::class => new CallableNodeRendererFactory(
                static function (ListNode $node) {
                    return new ListNodeRenderer($node, new ListRenderer($node));
                }
            ),
            MetaNode::class => new CallableNodeRendererFactory(
                static function (MetaNode $node) {
                    return new MetaNodeRenderer($node);
                }
            ),
            ParagraphNode::class => new CallableNodeRendererFactory(
                static function (ParagraphNode $node) {
                    return new ParagraphNodeRenderer($node);
                }
            ),
            QuoteNode::class => new CallableNodeRendererFactory(
                static function (QuoteNode $node) {
                    return new QuoteNodeRenderer($node);
                }
            ),
            SeparatorNode::class => new CallableNodeRendererFactory(
                static function (SeparatorNode $node) {
                    return new SeparatorNodeRenderer($node);
                }
            ),
            TableNode::class => new CallableNodeRendererFactory(
                static function (TableNode $node) {
                    return new TableNodeRenderer($node);
                }
            ),
            TitleNode::class => new CallableNodeRendererFactory(
                static function (TitleNode $node) {
                    return new TitleNodeRenderer($node);
                }
            ),
            TocNode::class => new CallableNodeRendererFactory(
                static function (TocNode $node) {
                    return new TocNodeRenderer($node);
                }
            ),
            CallableNode::class => new CallableNodeRendererFactory(
                static function (CallableNode $node) {
                    return new CallableNodeRenderer(
                        $node
                    );
                }
            ),
            SectionBeginNode::class => new CallableNodeRendererFactory(
                static function (SectionBeginNode $node) {
                    return new SectionBeginNodeRenderer($node);
                }
            ),
            SectionEndNode::class => new CallableNodeRendererFactory(
                static function (SectionEndNode $node) {
                    return new SectionEndNodeRenderer($node);
                }
            ),
        ];

        $nodeRendererFactories[DocumentNode::class] = new CallableNodeRendererFactory(
            static function (DocumentNode $node) {
                return new DocumentNodeRenderer($node);
            }
        );

        $nodeRendererFactories[CodeNode::class] = new CallableNodeRendererFactory(
            static function (CodeNode $node) {
                return new CodeNodeRenderer($node);
            }
        );

        $nodeRendererFactories[SpanNode::class] = new CallableNodeRendererFactory(
            static function (SpanNode $node) {
                return new SpanNodeRenderer($node);
            }
        );

        return $nodeRendererFactories;
    }
}
