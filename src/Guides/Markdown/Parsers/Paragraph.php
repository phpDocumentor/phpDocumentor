<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Markdown\Parsers;

use League\CommonMark\Block\Element\Paragraph as CommonMarkParagraph;
use League\CommonMark\Node\NodeWalker;
use League\CommonMark\Node\NodeWalkerEvent;
use phpDocumentor\Guides\Nodes;
use phpDocumentor\Guides\Nodes\ParagraphNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\Parser;

use function get_class;

final class Paragraph extends AbstractBlock
{
    /**
     * @return Nodes\ParagraphNode
     */
    public function parse(Parser $parser, NodeWalker $walker): Nodes\Node
    {
        $context = new ParagraphNode(new SpanNode($parser->getEnvironment(), ''));

        while ($event = $walker->next()) {
            $node = $event->getNode();

            if ($event->isEntering()) {
                continue;
            }

            if ($node instanceof CommonMarkParagraph) {
                return $context;
            }

            echo 'PARAGRAPH CONTEXT: I am '
                . 'leaving'
                . ' a '
                . get_class($node)
                . ' node'
                . "\n";
        }

        return $context;
    }

    public function supports(NodeWalkerEvent $event): bool
    {
        return $event->isEntering() && $event->getNode() instanceof CommonMarkParagraph;
    }
}
