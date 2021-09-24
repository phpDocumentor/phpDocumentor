<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Subparsers;

use Doctrine\Common\EventManager;
use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;

final class CodeParser implements Subparser
{
    /** @var Parser\Buffer */
    private $buffer;

    /** @var LineChecker */
    private $lineChecker;

    public function __construct(Parser $parser, EventManager $eventManager, Parser\Buffer $buffer)
    {
        $this->lineChecker = new LineChecker(new LineDataParser($parser, $eventManager));
        $this->buffer = $buffer;
    }

    public function parse(string $line): bool
    {
        if (!$this->lineChecker->isBlockLine($line)) {
            return false;
        }

        $this->buffer->push($line);

        return true;
    }

    /**
     * @return CodeNode
     */
    public function build(): ?Node
    {
        return new CodeNode($this->buffer->getLines());
    }
}
