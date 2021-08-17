<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

use function in_array;
use function preg_match;
use function preg_replace;
use function str_repeat;
use function strlen;
use function strpos;
use function trim;

class LineChecker
{
    private const HEADER_LETTERS = ['=', '-', '~', '*', '+', '^', '"', '.', '`', "'", '_', '#', ':'];

    /**
     * A regex matching all bullet list markers and a subset of the enumerated list markers.
     *
     * @see https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#bullet-lists
     * @see https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#enumerated-lists
     */
    public const LIST_MARKER = '/
        ^(
            [-+*\x{2022}\x{2023}\x{2043}]     # match bullet list markers: "*", "+", "-", "•", "‣", or "⁃"
            |(?:[\d#]+\.|[\d#]+\)|\([\d#]+\))
             # match arabic (1-9) or auto-enumerated ("#") lists with formats: "1.", "1)", or "(1)"
        )
        (?:\s+|$)
         # capture the spaces between marker and text to determine the list item text offset
         # (or eol, if text starts on a new line)
        /ux';

    public function isSpecialLine(string $line): ?string
    {
        if (strlen($line) < 2) {
            return null;
        }

        $letter = $line[0];

        if (!in_array($letter, self::HEADER_LETTERS, true)) {
            return null;
        }

        for ($i = 1; $i < strlen($line); $i++) {
            if ($line[$i] !== $letter) {
                return null;
            }
        }

        return $letter;
    }

    /**
     * Checks if this line is the start of a list item.
     *
     * @see self::LIST_MARKER
     *
     * @param string|null $listMarker if provided, this function only returns "true" if the
     *                                same list marker format is used (e.g. all dashes).
     * @param int|null    $listOffset if this line is a list, this will be set to the column
     *                                number of the start of the list item content (used to
     *                                match multiline items)
     * @param string|null $nextLine   if set, this line must also be a valid list line or
     *                                indented content for enumerated lists
     */
    public function isListLine(
        string $line,
        ?string &$listMarker = null,
        ?int &$listOffset = 0,
        ?string $nextLine = null
    ): bool {
        $isList = preg_match(self::LIST_MARKER, $line, $m) > 0;
        if (!$isList) {
            return false;
        }

        $offset           = strlen($m[0]);
        $normalizedMarker = preg_replace('/\d+/', 'd', $m[1]);
        if (
            // validate if next line can be considered part of a list for enumerated lists
            $normalizedMarker !== $m[1]
            && $nextLine !== null
            && trim($nextLine) !== ''
            && !$this->isBlockLine($nextLine, $offset)
            && !$this->isListLine($nextLine, $normalizedMarker)
        ) {
            return false;
        }

        if ($listMarker !== null) {
            $isList = $normalizedMarker === $listMarker;
        }

        if ($isList) {
            $listOffset = $offset;
            $listMarker = $normalizedMarker;
        }

        return $isList;
    }

    /**
     * Is this line "indented"?
     *
     * A blank line also counts as a "block" line, as it
     * may be the empty line between, for example, a
     * ".. note::" directive and the indented content on the
     * next lines.
     *
     * @param int $minIndent can be used to require a specific level of
     *                       indentation for non-blank lines (number of spaces)
     */
    public function isBlockLine(string $line, int $minIndent = 1): bool
    {
        return trim($line) === '' || $this->isIndented($line, $minIndent);
    }

    public function isComment(string $line): bool
    {
        return preg_match('/^\.\. (.*)$/mUsi', $line) > 0;
    }

    public function isDirective(string $line): bool
    {
        return preg_match('/^\.\. (\|(.+)\| |)([^\s]+)::( (.*)|)$/mUsi', $line) > 0;
    }

    /**
     * Check if line is an indented one.
     *
     * This does *not* include blank lines, use {@see isBlockLine()} to check
     * for blank or indented lines.
     *
     * @param int $minIndent can be used to require a specific level of indentation (number of spaces)
     */
    public function isIndented(string $line, int $minIndent = 1): bool
    {
        return strpos($line, str_repeat(' ', $minIndent)) === 0;
    }

    /**
     * Checks if the current line can be considered part of the definition list.
     *
     * Either the current line, or the next line must be indented to be considered
     * definition.
     *
     * @see https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#definition-lists
     */
    public function isDefinitionListEnded(string $line, string $nextLine): bool
    {
        if (trim($line) === '') {
            return false;
        }

        if ($this->isIndented($line)) {
            return false;
        }

        return !$this->isIndented($nextLine);
    }
}
