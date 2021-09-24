<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Subparsers;

use Doctrine\Common\EventManager;
use Exception;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\TableNode;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\LineDataParser;

use function trim;

final class TableParser implements Subparser
{
    /** @var Parser */
    private $parser;

    /** @var TableNode */
    private $nodeBuffer;

    /** @var Parser\TableParser */
    private $tableParser;

    /** @var LineChecker */
    private $lineChecker;

    /** @var Parser\TableSeparatorLineConfig */
    private $tableSeparatorLineConfig;

    public function __construct(
        Parser $parser,
        EventManager $eventManager,
        Parser\TableSeparatorLineConfig $tableSeparatorLineConfig
    ) {
        $this->parser = $parser;
        $this->lineChecker = new LineChecker(new LineDataParser($parser, $eventManager));
        $this->tableParser = new Parser\TableParser();
        $this->tableSeparatorLineConfig = $tableSeparatorLineConfig;
    }

    public function reset(string $openingLine): void
    {
        $this->nodeBuffer = new TableNode(
            $this->tableSeparatorLineConfig,
            $this->tableParser->guessTableType($openingLine)
        );
    }

    public function parse(string $line): bool
    {
        if (trim($line) === '') {
            return false;
        }

        $separatorLineConfig = $this->tableParser->parseTableSeparatorLine($line);

        // not sure if this is possible, being cautious
        if (!$this->nodeBuffer instanceof TableNode) {
            throw new Exception('Node Buffer should be a TableNode instance');
        }

        // push the separator or content line onto the TableNode
        if ($separatorLineConfig !== null) {
            $this->nodeBuffer->pushSeparatorLine($separatorLineConfig);
        } else {
            $this->nodeBuffer->pushContentLine($line);
        }

        return true;
    }

    /**
     * @return TableNode
     */
    public function build(): ?Node
    {
        $this->nodeBuffer->finalize($this->parser, $this->lineChecker);

        return $this->nodeBuffer;
    }
}
