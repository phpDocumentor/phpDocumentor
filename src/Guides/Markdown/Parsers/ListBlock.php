<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Markdown\Parsers;

use League\CommonMark\Block\Element\ListBlock as CommonMarkListBlock;
use League\CommonMark\Node\NodeWalker;
use League\CommonMark\Node\NodeWalkerEvent;
use phpDocumentor\Guides\Nodes\ListNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Parser;

use function get_class;

final class ListBlock extends AbstractBlock
{
    /**
     * @return ListNode
     */
    public function parse(Parser $parser, NodeWalker $walker): Node
    {
        $context = new ListNode();

        while ($event = $walker->next()) {
            $node = $event->getNode();

            if ($event->isEntering()) {
                continue;
            }

            if ($node instanceof CommonMarkListBlock) {
                return $context;
            }

            echo 'LIST CONTEXT: I am '
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
        return $event->isEntering() && $event->getNode() instanceof CommonMarkListBlock;
    }
}
