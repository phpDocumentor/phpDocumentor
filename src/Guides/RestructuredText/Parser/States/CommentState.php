<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\States;

use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\Lines;
use phpDocumentor\Guides\RestructuredText\Parser\State as StateName;

final class CommentState implements State
{
    /** @var Buffer */
    private $buffer;

    /** @var LineChecker */
    private $lineChecker;

    public function __construct(LineChecker $lineChecker)
    {
        $this->lineChecker = $lineChecker;
    }

    public function getName() : string
    {
        return StateName::COMMENT;
    }

    public function enter(Buffer $buffer, array $extraState = []) : void
    {
        $this->buffer = $buffer;
    }

    public function parse(string $line) : bool
    {
        return !$this->lineChecker->isComment($line) && (trim($line) === '' || $line[0] !== ' ');
    }

    public function leave() : ?Node
    {
        return null;
    }
}
