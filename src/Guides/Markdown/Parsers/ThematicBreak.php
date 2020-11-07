<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Markdown\Parsers;

use League\CommonMark\Block\Element\ThematicBreak as CommonMark;
use League\CommonMark\Node\NodeWalker;
use League\CommonMark\Node\NodeWalkerEvent;
use phpDocumentor\Guides\Nodes\Factory;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Parser;

final class ThematicBreak extends AbstractBlock
{
    public function __construct(Factory $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
    }

    public function parse(Parser $parser, NodeWalker $walker) : Node
    {
        return $this->nodeFactory->createSeparatorNode(1);
    }

    public function supports(NodeWalkerEvent $event) : bool
    {
        return !$event->isEntering() && $event->getNode() instanceof CommonMark;
    }
}
