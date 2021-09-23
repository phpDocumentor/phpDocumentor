<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Subparsers;

use Doctrine\Common\EventManager;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;

use function trim;

final class CommentParser implements Subparser
{
    /** @var LineChecker */
    private $lineChecker;

    public function __construct(Parser $parser, EventManager $eventManager)
    {
        $this->lineChecker = new LineChecker(new LineDataParser($parser, $eventManager));
    }

    public function parse(string $line): bool
    {
        return $this->lineChecker->isComment($line) || (trim($line) !== '' && $line[0] === ' ');
    }

    public function build(): ?Node
    {
        return null;
    }
}
