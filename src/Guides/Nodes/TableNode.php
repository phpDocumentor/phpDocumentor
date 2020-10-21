<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\Nodes;

use Exception;
use LogicException;
use phpDocumentor\Guides\Nodes\Table\TableColumn;
use phpDocumentor\Guides\Nodes\Table\TableRow;
use phpDocumentor\Guides\RestructuredText\Exception\InvalidTableStructure;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\TableSeparatorLineConfig;
use function array_keys;
use function array_reverse;
use function array_values;
use function count;
use function explode;
use function implode;
use function ksort;
use function max;
use function preg_match;
use function sprintf;
use function str_repeat;
use function strlen;
use function strpos;
use function substr;
use function trim;
use function utf8_decode;

class TableNode extends Node
{
    public const TYPE_SIMPLE = 'simple';
    public const TYPE_PRETTY = 'pretty';

    /** @var TableSeparatorLineConfig[] */
    private $separatorLineConfigs = [];

    /** @var string[] */
    private $rawDataLines = [];

    /** @var int */
    private $currentLineNumber = 0;

    /** @var bool */
    private $isCompiled = false;

    /** @var TableRow[] */
    protected $data = [];

    /** @var bool[] */
    protected $headers = [];

    /** @var string[] */
    private $errors = [];

    /** @var string */
    protected $type;

    /** @var LineChecker */
    private $lineChecker;

    public function __construct(TableSeparatorLineConfig $separatorLineConfig, string $type, LineChecker $lineChecker)
    {
        parent::__construct();

        $this->pushSeparatorLine($separatorLineConfig);
        $this->type = $type;
        $this->lineChecker = $lineChecker;
    }

    public function getCols() : int
    {
        if ($this->isCompiled === false) {
            throw new LogicException('Call compile() first.');
        }

        $columns = 0;
        foreach ($this->data as $row) {
            $columns = max($columns, count($row->getColumns()));
        }

        return $columns;
    }

    public function getRows() : int
    {
        if ($this->isCompiled === false) {
            throw new LogicException('Call compile() first.');
        }

        return count($this->data);
    }

    /**
     * @return TableRow[]
     */
    public function getData() : array
    {
        if ($this->isCompiled === false) {
            throw new LogicException('Call compile() first.');
        }

        return $this->data;
    }

    /**
     * Returns an of array of which rows should be headers,
     * where the row index is the key of the array and
     * the value is always true.
     *
     * @return bool[]
     */
    public function getHeaders() : array
    {
        if ($this->isCompiled === false) {
            throw new LogicException('Call compile() first.');
        }

        return $this->headers;
    }

    public function pushSeparatorLine(TableSeparatorLineConfig $separatorLineConfig) : void
    {
        if ($this->isCompiled === true) {
            throw new LogicException('Cannot push data after TableNode is compiled');
        }

        $this->separatorLineConfigs[$this->currentLineNumber] = $separatorLineConfig;
        $this->currentLineNumber++;
    }

    public function pushContentLine(string $line) : void
    {
        if ($this->isCompiled === true) {
            throw new LogicException('Cannot push data after TableNode is compiled');
        }

        $this->rawDataLines[$this->currentLineNumber] = utf8_decode($line);
        $this->currentLineNumber++;
    }

    public function finalize(Parser $parser) : void
    {
        if ($this->isCompiled === false) {
            $this->compile();
        }

        $tableAsString = $this->getTableAsString();

        if (count($this->errors) > 0) {
            $parser->getEnvironment()
                ->addError(sprintf("%s\nin file %s\n\n%s", $this->errors[0], $parser->getFilename(), $tableAsString));

            $this->data = [];
            $this->headers = [];

            return;
        }

        foreach ($this->data as $i => $row) {
            foreach ($row->getColumns() as $col) {
                $lines = explode("\n", $col->getContent());

                if ($this->lineChecker->isListLine($lines[0], false)) {
                    $node = $parser->parseFragment($col->getContent())->getNodes()[0];
                } else {
                    $node = $parser->createSpanNode($col->getContent());
                }

                $col->setNode($node);
            }
        }
    }

    /**
     * Looks at all the raw data and finally populates the data
     * and headers.
     */
    private function compile() : void
    {
        $this->isCompiled = true;

        if ($this->type === self::TYPE_SIMPLE) {
            $this->compileSimpleTable();
        } else {
            $this->compilePrettyTable();
        }
    }

    private function compileSimpleTable() : void
    {
        // determine if there is second === separator line (other than
        // the last line): this would mean there are header rows
        $finalHeadersRow = 0;
        foreach ($this->separatorLineConfigs as $i => $separatorLine) {
            // skip the first line: we're looking for the *next* line
            if ($i === 0) {
                continue;
            }

            // we found the next ==== line
            if ($separatorLine->getLineCharacter() === '=') {
                // found the end of the header rows
                $finalHeadersRow = $i;

                break;
            }
        }

        // if the final header row is *after* the last data line, it's not
        // really a header "ending" and so there are no headers
        $lastDataLineNumber = array_keys($this->rawDataLines)[count($this->rawDataLines) - 1];
        if ($finalHeadersRow > $lastDataLineNumber) {
            $finalHeadersRow = 0;
        }

        // todo - support "---" in the future for colspan
        $columnRanges = $this->separatorLineConfigs[0]->getPartRanges();
        $lastColumnRangeEnd = array_values($columnRanges)[count($columnRanges) - 1][1];
        foreach ($this->rawDataLines as $i => $line) {
            $row = new TableRow();
            // loop over where all the columns should be

            $previousColumnEnd = null;
            foreach ($columnRanges as $columnRange) {
                $isRangeBeyondText = $columnRange[0] >= strlen($line);
                // check for content in the "gap"
                if ($previousColumnEnd !== null && !$isRangeBeyondText) {
                    $gapText = substr($line, $previousColumnEnd, $columnRange[0] - $previousColumnEnd);
                    if (strlen(trim($gapText)) !== 0) {
                        $this->addError(
                            sprintf('Malformed table: content "%s" appears in the "gap" on row "%s"', $gapText, $line)
                        );
                    }
                }

                if ($isRangeBeyondText) {
                    // the text for this line ended earlier. This column should be blank

                    $content = '';
                } elseif ($lastColumnRangeEnd === $columnRange[1]) {
                    // this is the last column, so get the rest of the line
                    // this is because content can go *beyond* the table legally
                    $content = substr(
                        $line,
                        $columnRange[0]
                    );
                } else {
                    $content = substr(
                        $line,
                        $columnRange[0],
                        $columnRange[1] - $columnRange[0]
                    );
                }

                $content = trim($content);
                $row->addColumn($content, 1);

                $previousColumnEnd = $columnRange[1];
            }

            // is header row?
            if ($i <= $finalHeadersRow) {
                $this->headers[$i] = true;
            }

            $this->data[$i] = $row;
        }

        /** @var TableRow|null $previousRow */
        $previousRow = null;
        // check for empty first columns, which means this is
        // not a new row, but the continuation of the previous row
        foreach ($this->data as $i => $row) {
            if ($row->getFirstColumn()->isCompletelyEmpty() && $previousRow !== null) {
                try {
                    $previousRow->absorbRowContent($row);
                } catch (InvalidTableStructure $e) {
                    $this->addError($e->getMessage());
                }

                unset($this->data[$i]);

                continue;
            }

            $previousRow = $row;
        }
    }

    private function compilePrettyTable() : void
    {
        // loop over ALL separator lines to find ALL of the column ranges
        $columnRanges = [];
        $finalHeadersRow = 0;
        foreach ($this->separatorLineConfigs as $rowIndex => $separatorLine) {
            if ($separatorLine->isHeader()) {
                if ($finalHeadersRow !== 0) {
                    $this->addError(
                        sprintf(
                            'Malformed table: multiple "header rows" using "===" were found. See table '
                            . 'lines "%d" and "%d"',
                            $finalHeadersRow + 1,
                            $rowIndex
                        )
                    );
                }

                // indicates that "=" was used
                $finalHeadersRow = $rowIndex - 1;
            }

            foreach ($separatorLine->getPartRanges() as $columnRange) {
                $colStart = $columnRange[0];
                $colEnd = $columnRange[1];

                // we don't have this "start" yet? just add it
                // in theory, should only happen for the first row
                if (!isset($columnRanges[$colStart])) {
                    $columnRanges[$colStart] = $colEnd;

                    continue;
                }

                // an exact column range we've already seen
                // OR, this new column goes beyond what we currently
                // have recorded, which means its a colspan, and so
                // we already have correctly recorded the "smallest"
                // current column ranges
                if ($columnRanges[$colStart] <= $colEnd) {
                    continue;
                }

                // this is not a new "start", but it is a new "end"
                // this means that we've found a "shorter" column that
                // we've seen before. We need to update the "end" of
                // the existing column, and add a "new" column
                $previousEnd = $columnRanges[$colStart];

                // A) update the end of this column to the new end
                $columnRanges[$colStart] = $colEnd;
                // B) add a new column from this new end, to the previous end
                $columnRanges[$colEnd + 1] = $previousEnd;
                ksort($columnRanges);
            }
        }

        /** @var TableRow[] $rows */
        $rows = [];
        $partialSeparatorRows = [];
        foreach ($this->rawDataLines as $rowIndex => $line) {
            $row = new TableRow();

            // if the row is part separator row, part content, this
            // is a rowspan situation - e.g.
            // |           +----------------+----------------------------+
            // look for +-----+ pattern
            if (preg_match('/\+[-]+\+/', $this->rawDataLines[$rowIndex]) === 1) {
                $partialSeparatorRows[$rowIndex] = true;
            }

            $currentColumnStart = null;
            $currentSpan = 1;
            $previousColumnEnd = null;
            foreach ($columnRanges as $start => $end) {
                // a content line that ends before it should
                if ($end >= strlen($line)) {
                    $this->errors[] = sprintf(
                        "Malformed table: Line\n\n%s\n\ndoes not appear to be a complete table row",
                        $line
                    );

                    break;
                }

                if ($currentColumnStart !== null) {
                    if ($previousColumnEnd === null) {
                        throw new LogicException('The previous column end is not set yet');
                    }

                    $gapText = substr($line, $previousColumnEnd, $start - $previousColumnEnd);
                    if (strpos($gapText, '|') === false && strpos($gapText, '+') === false) {
                        // text continued through the "gap". This is a colspan
                        // "+" is an odd character - it's usually "|", but "+" can
                        // happen in row-span situations
                        $currentSpan++;
                    } else {
                        // we just hit a proper "gap" record the line up until now
                        $row->addColumn(
                            substr($line, $currentColumnStart, $previousColumnEnd - $currentColumnStart),
                            $currentSpan
                        );
                        $currentSpan = 1;
                        $currentColumnStart = null;
                    }
                }

                // if the current column start is null, then set it
                // other wise, leave it - this is a colspan, and eventually
                // we want to get all the text starting here
                if ($currentColumnStart === null) {
                    $currentColumnStart = $start;
                }

                $previousColumnEnd = $end;
            }

            // record the last column
            if ($currentColumnStart !== null) {
                if ($previousColumnEnd === null) {
                    throw new LogicException('The previous column end is not set yet');
                }

                $row->addColumn(
                    substr($line, $currentColumnStart, $previousColumnEnd - $currentColumnStart),
                    $currentSpan
                );
            }

            $rows[$rowIndex] = $row;
        }

        $columnIndexesCurrentlyInRowspan = [];
        foreach ($rows as $rowIndex => $row) {
            if (isset($partialSeparatorRows[$rowIndex])) {
                // this row is part content, part separator due to a rowspan
                // for each column that contains content, we need to
                // push it onto the last real row's content and record
                // that this column in the next row should also be
                // included in that previous row's content
                foreach ($row->getColumns() as $columnIndex => $column) {
                    if (!$column->isCompletelyEmpty()
                        && str_repeat(
                            '-',
                            strlen($column->getContent())
                        ) === $column->getContent()) {
                        // only a line separator in this column - not content!
                        continue;
                    }

                    $prevTargetColumn = $this->findColumnInPreviousRows((int) $columnIndex, $rows, (int) $rowIndex);
                    $prevTargetColumn->addContent("\n" . $column->getContent());
                    $prevTargetColumn->incrementRowSpan();
                    // mark that this column on the next row should also be added
                    // to the previous row
                    $columnIndexesCurrentlyInRowspan[] = $columnIndex;
                }

                // remove the row - it's not real
                unset($rows[$rowIndex]);

                continue;
            }

            // check if the previous row was a partial separator row, and
            // we need to take some columns and add them to a previous row's content
            foreach ($columnIndexesCurrentlyInRowspan as $columnIndex) {
                $prevTargetColumn = $this->findColumnInPreviousRows($columnIndex, $rows, (int) $rowIndex);
                $columnInRowspan = $row->getColumn($columnIndex);
                if ($columnInRowspan === null) {
                    throw new LogicException('Cannot find column for index "%s"', $columnIndex);
                }

                $prevTargetColumn->addContent("\n" . $columnInRowspan->getContent());

                // now this column actually needs to be removed from this row,
                // as it's not a real column that needs to be printed
                $row->removeColumn($columnIndex);
            }

            $columnIndexesCurrentlyInRowspan = [];

            // if the next row is just $i+1, it means there
            // was no "separator" and this is really just a
            // continuation of this row.
            $nextRowCounter = 1;
            while (isset($rows[(int) $rowIndex + $nextRowCounter])) {
                // but if the next line is actually a partial separator, then
                // it is not a continuation of the content - quit now
                if (isset($partialSeparatorRows[(int) $rowIndex + $nextRowCounter])) {
                    break;
                }

                $targetRow = $rows[(int) $rowIndex + $nextRowCounter];
                unset($rows[(int) $rowIndex + $nextRowCounter]);

                try {
                    $row->absorbRowContent($targetRow);
                } catch (InvalidTableStructure $e) {
                    $this->addError($e->getMessage());
                }

                $nextRowCounter++;
            }
        }

        // one more loop to set headers
        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex > $finalHeadersRow) {
                continue;
            }

            $this->headers[$rowIndex] = true;
        }

        $this->data = $rows;
    }

    private function getTableAsString() : string
    {
        $lines = [];
        $i = 0;
        while (isset($this->separatorLineConfigs[$i]) || isset($this->rawDataLines[$i])) {
            if (isset($this->separatorLineConfigs[$i])) {
                $lines[] = $this->separatorLineConfigs[$i]->getRawContent();
            } else {
                $lines[] = $this->rawDataLines[$i];
            }

            $i++;
        }

        return implode("\n", $lines);
    }

    private function addError(string $message) : void
    {
        $this->errors[] = $message;
    }

    /**
     * @param TableRow[] $rows
     */
    private function findColumnInPreviousRows(int $columnIndex, array $rows, int $currentRowIndex) : TableColumn
    {
        /** @var TableRow[] $reversedRows */
        $reversedRows = array_reverse($rows, true);

        // go through the rows backwards to find the last/previous
        // row that actually had a real column at this position
        foreach ($reversedRows as $k => $row) {
            // start by skipping any future rows, as we go backward
            if ($k >= $currentRowIndex) {
                continue;
            }

            $prevTargetColumn = $row->getColumn($columnIndex);
            if ($prevTargetColumn !== null) {
                return $prevTargetColumn;
            }
        }

        throw new Exception('Could not find column in any previous rows');
    }
}
