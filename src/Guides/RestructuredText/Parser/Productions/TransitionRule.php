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

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SeparatorNode;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;
use phpDocumentor\Guides\RestructuredText\Parser\LinesIterator;

use function in_array;
use function strlen;
use function trim;

/**
 * @link https://docutils.sourceforge.io/docs/ref/rst/restructuredtext.html#transitions
 */
final class TransitionRule implements Rule
{
    private const HEADER_LETTERS = [
        '!',
        '"',
        '#',
        '$',
        '%',
        '&',
        '\'',
        '(',
        ')',
        '*',
        '+',
        ',',
        '-',
        '.',
        '/',
        ':',
        ';',
        '<',
        '=',
        '>',
        '?',
        '@',
        '[',
        '\\',
        ']',
        '^',
        '_',
        '`',
        '{',
        '|',
        '}',
        '~',
    ];

    public function applies(DocumentParser $documentParser): bool
    {
        $line = $documentParser->getDocumentIterator()->current();
        $nextLine = $documentParser->getDocumentIterator()->getNextLine();

        return $this->currentLineIsASeparator($line, $nextLine) !== null;
    }

    public function apply(LinesIterator $documentIterator, ?Node $on = null): ?Node
    {
        $overlineLetter = $this->currentLineIsASeparator(
            $documentIterator->current(),
            $documentIterator->getNextLine()
        );

        if ($overlineLetter !== null) {
            $documentIterator->next();
        }

        return new SeparatorNode(1);
    }

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

    private function currentLineIsASeparator(string $line, ?string $nextLine): ?string
    {
        $letter = $this->isSpecialLine($line);
        if ($nextLine !== null && $letter && $this->isWhiteLine($nextLine)) {
            return $letter;
        }

        return null;
    }

    private function isWhiteLine(string $line): bool
    {
        return trim($line) === '';
    }
}
