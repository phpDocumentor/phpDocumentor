<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\States;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\TableNode;
use phpDocumentor\Guides\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\Lines;
use phpDocumentor\Guides\RestructuredText\Parser\State as StateName;
use phpDocumentor\Guides\RestructuredText\Parser\TableParser;

final class TableState implements State
{
    /** @var Buffer */
    private $buffer;

    /** @var TableParser */
    private $tableParser;

    /** @var TableNode */
    private $nodeBuffer;

    /** @var LineChecker */
    private $lineChecker;

    /** @var Parser */
    private $parser;

    public function __construct(TableParser $tableParser, LineChecker $lineChecker, Parser $parser)
    {
        $this->tableParser = $tableParser;
        $this->lineChecker = $lineChecker;
        $this->parser = $parser;
    }

    public function getName() : string
    {
        return StateName::TABLE;
    }

    public function enter(Buffer $buffer, array $extraState = []) : void
    {
        $this->buffer = $buffer;

        $tableNode = new TableNode(
            $extraState['separatorLineConfig'],
            $this->tableParser->guessTableType($extraState['line']),
            $this->lineChecker
        );

        $this->nodeBuffer = $tableNode;
    }

    public function parse(string $line) : bool
    {
        if (trim($line) === '') {
            return false;
        }

        $separatorLineConfig = $this->tableParser->parseTableSeparatorLine($line);

        // push the separator or content line onto the TableNode
        if ($separatorLineConfig !== null) {
            $this->nodeBuffer->pushSeparatorLine($separatorLineConfig);
        } else {
            $this->nodeBuffer->pushContentLine($line);
        }

        return true;
    }

    public function leave() : ?Node
    {
        $node = $this->nodeBuffer;
        $node->finalize($this->parser);

        return $node;
    }
}
