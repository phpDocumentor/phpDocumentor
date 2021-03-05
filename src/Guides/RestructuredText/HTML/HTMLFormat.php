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

use phpDocumentor\Guides\Environment;
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
use phpDocumentor\Guides\Nodes\UmlNode;
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
use phpDocumentor\Guides\Renderers\UmlNodeRenderer;
use phpDocumentor\Guides\RestructuredText;
use phpDocumentor\Guides\RestructuredText\Formats\Format;
use phpDocumentor\Transformer\Writer\Graph\PlantumlRenderer;

final class HTMLFormat implements Format
{
    /** @var PlantumlRenderer */
    private $plantumlRenderer;

    public function __construct(PlantumlRenderer $plantumlRenderer)
    {
        $this->plantumlRenderer = $plantumlRenderer;
    }

    public function getFileExtension() : string
    {
        return Format::HTML;
    }

    public function getDirectives() : array
    {
        return [
            new RestructuredText\HTML\Directives\Image(),
            new RestructuredText\HTML\Directives\Figure(),
            new RestructuredText\HTML\Directives\Uml(),
            new RestructuredText\HTML\Directives\Meta(),
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
    public function getNodeRendererFactories(Environment $environment) : array
    {
        $renderer = $environment->getRenderer();

        $nodeRendererFactories = [
            AnchorNode::class => new CallableNodeRendererFactory(
                static function () use ($renderer) {
                    return new AnchorNodeRenderer($renderer);
                }
            ),
            DefinitionListNode::class => new CallableNodeRendererFactory(
                static function () use ($renderer)  {
                    return new DefinitionListNodeRenderer($renderer);
                }
            ),
            FigureNode::class => new CallableNodeRendererFactory(
                static function () use ($renderer)  {
                    return new FigureNodeRenderer($renderer);
                }
            ),
            UmlNode::class => new CallableNodeRendererFactory(
                function () use ($renderer)  {
                    return new UmlNodeRenderer($this->plantumlRenderer, $renderer);
                }
            ),
            ImageNode::class => new CallableNodeRendererFactory(
                static function () use ($renderer)  {
                    return new ImageNodeRenderer($renderer);
                }
            ),
            ListNode::class => new CallableNodeRendererFactory(
                static function () use ($renderer)  {
                    return new ListNodeRenderer(new ListRenderer($renderer));
                }
            ),
            MetaNode::class => new CallableNodeRendererFactory(
                static function () use ($renderer)  {
                    return new MetaNodeRenderer($renderer);
                }
            ),
            ParagraphNode::class => new CallableNodeRendererFactory(
                static function () use ($renderer)  {
                    return new ParagraphNodeRenderer($renderer);
                }
            ),
            QuoteNode::class => new CallableNodeRendererFactory(
                static function () use ($renderer)  {
                    return new QuoteNodeRenderer($renderer);
                }
            ),
            SeparatorNode::class => new CallableNodeRendererFactory(
                static function () use ($renderer)  {
                    return new SeparatorNodeRenderer($renderer);
                }
            ),
            TableNode::class => new CallableNodeRendererFactory(
                static function () use ($renderer)  {
                    return new TableNodeRenderer($renderer);
                }
            ),
            TitleNode::class => new CallableNodeRendererFactory(
                static function () use ($renderer)  {
                    return new TitleNodeRenderer($renderer);
                }
            ),
            TocNode::class => new CallableNodeRendererFactory(
                static function () use ($environment)  {
                    return new TocNodeRenderer($environment);
                }
            ),
            CallableNode::class => new CallableNodeRendererFactory(
                static function ()  {
                    return new CallableNodeRenderer();
                }
            ),
            SectionBeginNode::class => new CallableNodeRendererFactory(
                static function () use ($renderer)  {
                    return new SectionBeginNodeRenderer($renderer);
                }
            ),
            SectionEndNode::class => new CallableNodeRendererFactory(
                static function () use ($renderer)  {
                    return new SectionEndNodeRenderer($renderer);
                }
            ),
        ];

        $nodeRendererFactories[DocumentNode::class] = new CallableNodeRendererFactory(
            static function () use ($environment)  {
                return new DocumentNodeRenderer($environment);
            }
        );

        $nodeRendererFactories[CodeNode::class] = new CallableNodeRendererFactory(
            static function () use ($renderer)  {
                return new CodeNodeRenderer($renderer);
            }
        );

        $nodeRendererFactories[SpanNode::class] = new CallableNodeRendererFactory(
            static function () use ($environment)  {
                return new SpanNodeRenderer($environment);
            }
        );

        return $nodeRendererFactories;
    }
}
