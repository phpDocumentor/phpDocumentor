<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

use InvalidArgumentException;
use phpDocumentor\Guides\Nodes\TableNode;
use function in_array;
use function sprintf;

final class TableSeparatorLineConfig
{
    /** @var bool */
    private $isHeader;

    /** @var string */
    private $tableType;

    /** @var int[][] */
    private $partRanges;

    /** @var string */
    private $lineCharacter;

    /** @var string */
    private $rawContent;

    /**
     * @param int[][] $partRanges
     */
    public function __construct(
        bool $isHeader,
        string $tableType,
        array $partRanges,
        string $lineCharacter,
        string $rawContent
    ) {
        if (!in_array($tableType, [TableNode::TYPE_SIMPLE, TableNode::TYPE_PRETTY], true)) {
            throw new InvalidArgumentException(sprintf('Invalid table type'));
        }

        if (!in_array($lineCharacter, ['=', '-'], true)) {
            throw new InvalidArgumentException(sprintf('Unexpected line character "%s"', $lineCharacter));
        }

        $this->isHeader = $isHeader;
        $this->tableType = $tableType;
        $this->partRanges = $partRanges;
        $this->lineCharacter = $lineCharacter;
        $this->rawContent = $rawContent;
    }

    public function isHeader() : bool
    {
        return $this->isHeader;
    }

    public function isSimpleTableType() : bool
    {
        return $this->tableType === TableNode::TYPE_SIMPLE;
    }

    /**
     * Returns an array of position "ranges" where content should exist.
     *
     * For example:
     *      ===   =====   === ===
     *
     * Would yield:
     *      [[0, 3], [6, 11], [14, 17], [18, 21]]
     *
     * @return int[][]
     */
    public function getPartRanges() : array
    {
        return $this->partRanges;
    }

    /**
     * Returns the "line" character used in the separator,
     * either - or =
     */
    public function getLineCharacter() : string
    {
        return $this->lineCharacter;
    }

    public function getRawContent() : string
    {
        return $this->rawContent;
    }
}
