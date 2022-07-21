<?php

declare(strict_types=1);

use phpDocumentor\Guides\Nodes;

return [
    Nodes\AnchorNode::class => 'anchor.html.twig',
    Nodes\FigureNode::class => 'figure.html.twig',
    Nodes\Metadata\MetaNode::class => 'meta.html.twig',
    Nodes\ParagraphNode::class => 'paragraph.html.twig',
    Nodes\QuoteNode::class => 'quote.html.twig',
    Nodes\SeparatorNode::class => 'separator.html.twig',
    Nodes\TitleNode::class => 'header-title.html.twig',
    Nodes\SectionBeginNode::class => 'section-begin.html.twig',
    Nodes\SectionEndNode::class => 'section-end.html.twig',
    Nodes\ImageNode::class => 'image.html.twig',
    Nodes\UmlNode::class => 'uml.html.twig',
    Nodes\CodeNode::class => 'code.html.twig',
    Nodes\DefinitionListNode::class => 'definition-list.html.twig',
    Nodes\ListNode::class => 'list.html.twig',
    Nodes\LiteralBlockNode::class => 'directives/literal-block.html.twig'
];
