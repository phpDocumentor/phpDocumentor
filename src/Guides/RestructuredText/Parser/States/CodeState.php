<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\States;

use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\State as StateName;

final class CodeState implements State
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
        return StateName::CODE;
    }

    public function enter(Buffer $buffer, array $extraState = []) : void
    {
        $this->buffer = $buffer;
    }

    public function parse(string $line) : bool
    {
        if (!$this->lineChecker->isBlockLine($line)) {
            return false;
        }

        $this->buffer->push($line);

        return true;
    }

    public function leave() : ?Node
    {
        /** @var string[] $buffer */
        $buffer = $this->buffer->getLines();

        return new CodeNode($buffer);
    }
}
