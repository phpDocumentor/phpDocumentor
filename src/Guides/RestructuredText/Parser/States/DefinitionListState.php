<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\States;

use phpDocumentor\Guides\Nodes\DefinitionListNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;
use phpDocumentor\Guides\RestructuredText\Parser\Lines;
use phpDocumentor\Guides\RestructuredText\Parser\State as StateName;

final class DefinitionListState implements State
{
    /** @var Buffer */
    private $buffer;

    /** @var LineChecker */
    private $lineChecker;

    /** @var LineDataParser */
    private $lineDataParser;

    /** @var Lines */
    private $lines;

    public function __construct(LineChecker $lineChecker, LineDataParser $lineDataParser, Lines $lines)
    {
        $this->lineChecker = $lineChecker;
        $this->lineDataParser = $lineDataParser;
        $this->lines = $lines;
    }

    public function getName() : string
    {
        return StateName::DEFINITION_LIST;
    }

    public function enter(Buffer $buffer, array $extraState = []) : void
    {
        $this->buffer = $buffer;
    }

    public function parse(string $line) : bool
    {
        if ($this->lineChecker->isDefinitionListEnded($line, $this->lines->getNextLine())) {
            return false;
        }

        $this->buffer->push($line);

        return true;
    }

    public function leave() : ?Node
    {
        $definitionList = $this->lineDataParser->parseDefinitionList(
            $this->buffer->getLines()
        );

        return new DefinitionListNode($definitionList);
    }
}
