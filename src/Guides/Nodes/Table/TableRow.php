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

namespace phpDocumentor\Guides\Nodes\Table;

use InvalidArgumentException;
use LogicException;
use phpDocumentor\Guides\RestructuredText\Exception\InvalidTableStructure;
use function array_map;
use function implode;
use function sprintf;

final class TableRow
{
    /** @var TableColumn[] */
    private $columns = [];

    public function addColumn(string $content, int $colSpan) : void
    {
        $this->columns[] = new TableColumn($content, $colSpan);
    }

    /**
     * @return TableColumn[]
     */
    public function getColumns() : array
    {
        return $this->columns;
    }

    public function getColumn(int $index) : ?TableColumn
    {
        return $this->columns[$index] ?? null;
    }

    public function getFirstColumn() : TableColumn
    {
        $column = $this->getColumn(0);

        if ($column === null) {
            throw new LogicException('Row has no columns');
        }

        return $column;
    }

    /**
     * Push the content from the columns of a row onto this row.
     *
     * Useful when we discover that a row is actually just a continuation
     * of this row, and so we want to copy the content to this row's
     * columns before removing the row.
     *
     * @throws InvalidTableStructure
     */
    public function absorbRowContent(TableRow $targetRow) : void
    {
        // iterate over each column and combine the content
        foreach ($this->getColumns() as $columnIndex => $column) {
            $targetColumn = $targetRow->getColumn($columnIndex);
            if ($targetColumn === null) {
                throw new InvalidTableStructure(
                    sprintf(
                        'Malformed table: lines "%s" and "%s" appear to be in the same row, '
                        . 'but don\'t share the same number of columns.',
                        $this->toString(),
                        $targetRow->toString()
                    )
                );
            }

            $column->addContent("\n" . $targetColumn->getContent());
        }
    }

    public function toString() : string
    {
        return implode(
            ' | ',
            array_map(
                static function (TableColumn $column) {
                    return $column->getContent();
                },
                $this->columns
            )
        );
    }

    public function removeColumn(int $columnIndex) : void
    {
        if ($this->getColumn($columnIndex) === null) {
            throw new InvalidArgumentException(sprintf('Bad column index "%d"', $columnIndex));
        }

        unset($this->columns[$columnIndex]);
    }
}
