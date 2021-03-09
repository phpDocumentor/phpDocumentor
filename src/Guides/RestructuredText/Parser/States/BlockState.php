<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\States;

use phpDocumentor\Guides\Nodes\BlockNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\QuoteNode;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\Lines;
use phpDocumentor\Guides\RestructuredText\Parser\State as StateName;

final class BlockState implements State
{
    /** @var Buffer */
    private $buffer;

    /** @var LineChecker */
    private $lineChecker;

    /** @var Parser */
    private $parser;

    public function __construct(LineChecker $lineChecker, Parser $parser)
    {
        $this->lineChecker = $lineChecker;
        $this->parser = $parser;
    }

    public function getName() : string
    {
        return StateName::BLOCK;
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
        /** @var string[] $lines */
        $lines = $this->buffer->getLines();

        $blockNode = new BlockNode($lines);

        $document = $this->parser->getSubParser()->parseLocal($blockNode->getValue());

        return new QuoteNode($document);
    }
}
