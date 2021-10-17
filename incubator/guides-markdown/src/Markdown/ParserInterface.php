<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Markdown;

use League\CommonMark\Node\NodeWalker;
use League\CommonMark\Node\NodeWalkerEvent;
use phpDocumentor\Guides\MarkupLanguageParser as GuidesParser;
use phpDocumentor\Guides\Nodes\Node;

interface ParserInterface
{
    public function parse(GuidesParser $parser, NodeWalker $walker): Node;

    public function supports(NodeWalkerEvent $event): bool;
}
