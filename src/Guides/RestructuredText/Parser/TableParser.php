<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

use Exception;
use phpDocumentor\Guides\Nodes\TableNode;
use function count;
use function in_array;
use function sprintf;
use function strlen;
use function trim;

class TableParser
{
    private const SIMPLE_TABLE_LETTER = '=';
    // "-" is valid as a separator in a simple table, except
    // on the first and last lines
    private const SIMPLE_TABLE_LETTER_ALT = '-';

    private const PRETTY_TABLE_LETTER = '-';

    private const PRETTY_TABLE_HEADER = '=';

    private const PRETTY_TABLE_JOINT = '+';

    /**
     * Parses a line from a table to see if it is a separator line.
     *
     * Returns TableSeparatorLineConfig if it *is* a separator, null otherwise.
     */
    public function parseTableSeparatorLine(string $line) : ?TableSeparatorLineConfig
    {
        $header = false;
        $pretty = false;
        $line = trim($line);

        if ($line === '') {
            return null;
        }

        // Finds the table chars
        $chars = $this->findTableChars($line);

        if ($chars === null) {
            return null;
        }

        if ($chars[0] === self::PRETTY_TABLE_JOINT && $chars[1] === self::PRETTY_TABLE_LETTER) {
            $pretty = true;
            // reverse the chars: - is the line char, + is the space char
            $chars = [self::PRETTY_TABLE_LETTER, self::PRETTY_TABLE_JOINT];
        } elseif ($chars[0] === self::PRETTY_TABLE_JOINT && $chars[1] === self::PRETTY_TABLE_HEADER) {
            $pretty = true;
            $header = true;
            // reverse the chars: = is the line char, + is the space char
            $chars = [self::PRETTY_TABLE_HEADER, self::PRETTY_TABLE_JOINT];
        } else {
            // either a simple table or not a separator line

            // if line char is not "=" or "-", not a separator line
            if (!in_array($chars[0], [self::SIMPLE_TABLE_LETTER, self::SIMPLE_TABLE_LETTER_ALT], true)) {
                return null;
            }

            // if space char is not a space, not a separator line
            if ($chars[1] !== ' ') {
                return null;
            }
        }

        $parts = [];
        /** @var int|null $currentPartStart */
        $currentPartStart = null;

        for ($i = 0; $i < strlen($line); $i++) {
            // we found the "line char": "-" or "="
            if ($line[$i] === $chars[0]) {
                if ($currentPartStart === null) {
                    $currentPartStart = $i;
                }

                continue;
            }

            if ($line[$i] !== $chars[1]) {
                throw new Exception(sprintf('Unexpected char "%s"', $line[$i]));
            }

            // found the "space" char
            // record the part "range" if we're at the end of a range
            if ($currentPartStart === null) {
                continue;
            }

            $parts[] = [$currentPartStart, $i];
            $currentPartStart = null;
        }

        // finish the last "part"
        if ($currentPartStart !== null) {
            $parts[] = [$currentPartStart, $i];
        }

        if (count($parts) > 1) {
            return new TableSeparatorLineConfig(
                $header,
                $pretty ? TableNode::TYPE_PRETTY : TableNode::TYPE_SIMPLE,
                $parts,
                $chars[0],
                $line
            );
        }

        return null;
    }

    public function guessTableType(string $line) : string
    {
        return $line[0] === self::SIMPLE_TABLE_LETTER ? TableNode::TYPE_SIMPLE : TableNode::TYPE_PRETTY;
    }

    /**
     * A "line" separator always has only two characters.
     * This method returns those two characters.
     *
     * This returns null if this is not a separator line
     * or it's malformed in any way.
     *
     * @return string[]|null
     */
    private function findTableChars(string $line) : ?array
    {
        $lineChar = $line[0];
        $spaceChar = null;

        for ($i = 0; $i < strlen($line); $i++) {
            if ($line[$i] === $lineChar) {
                continue;
            }

            if ($spaceChar === null) {
                $spaceChar = $line[$i];

                continue;
            }

            if ($line[$i] !== $spaceChar) {
                return null;
            }
        }

        if ($spaceChar === null) {
            return null;
        }

        return [$lineChar, $spaceChar];
    }
}
