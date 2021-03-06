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
use phpDocumentor\Guides\Renderers\DefaultNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\DefinitionListNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\DocumentNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\ListRenderer;
use phpDocumentor\Guides\Renderers\Html\SpanNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\TableNodeRenderer;
use phpDocumentor\Guides\Renderers\Html\TocNodeRenderer;
use phpDocumentor\Guides\Renderers\InMemoryNodeRendererFactory;
use phpDocumentor\Guides\Renderers\ListNodeRenderer;
use phpDocumentor\Guides\Renderers\NodeRendererFactory;
use phpDocumentor\Guides\Renderers\TemplateNodeRenderer;
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

    public function getNodeRendererFactory(Environment $environment) : NodeRendererFactory
    {
        $renderer = $environment->getRenderer();

        return new InMemoryNodeRendererFactory(
            [
                AnchorNode::class => new TemplateNodeRenderer($renderer, 'anchor.html.twig'),
                FigureNode::class => new TemplateNodeRenderer($renderer, 'figure.html.twig'),
                MetaNode::class => new TemplateNodeRenderer($renderer, 'meta.html.twig'),
                ParagraphNode::class => new TemplateNodeRenderer($renderer, 'paragraph.html.twig'),
                QuoteNode::class => new TemplateNodeRenderer($renderer, 'quote.html.twig'),
                SeparatorNode::class => new TemplateNodeRenderer($renderer, 'separator.html.twig'),
                TitleNode::class => new TemplateNodeRenderer($renderer, 'header-title.html.twig'),
                SectionBeginNode::class => new TemplateNodeRenderer($renderer, 'section-begin.html.twig'),
                SectionEndNode::class => new TemplateNodeRenderer($renderer, 'section-end.html.twig'),
                ImageNode::class => new TemplateNodeRenderer($renderer, 'image.html.twig'),
                UmlNode::class => new TemplateNodeRenderer($renderer, 'uml.html.twig'),
                CodeNode::class => new TemplateNodeRenderer($renderer, 'code.html.twig'),
                DefinitionListNode::class => new DefinitionListNodeRenderer($renderer),
                ListNode::class => new ListNodeRenderer(new ListRenderer($renderer), $environment),
                TableNode::class => new TableNodeRenderer($renderer),
                TocNode::class => new TocNodeRenderer($environment),
                CallableNode::class => new CallableNodeRenderer(),
                DocumentNode::class => new DocumentNodeRenderer($environment),
                SpanNode::class => new SpanNodeRenderer($environment),
            ],
            new DefaultNodeRenderer($environment)
        );
    }
}
