<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions;

use phpDocumentor\Guides\Nodes\BlockNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\QuoteNode;
use phpDocumentor\Guides\Nodes\SectionBeginNode;
use phpDocumentor\Guides\Nodes\SectionEndNode;
use phpDocumentor\Guides\Nodes\SeparatorNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\Nodes\TitleNode;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\Buffer;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentIterator;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParser;

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
        '~'
    ];

    public function applies(DocumentParser $documentParser): bool
    {
        $line = $documentParser->getDocumentIterator()->current();
        $nextLine = $documentParser->getDocumentIterator()->getNextLine();

        return $this->currentLineIsASeparator($line, $nextLine) !== null;
    }

    public function apply(DocumentIterator $documentIterator): ?Node
    {
        $overlineLetter = $this->currentLineIsASeparator($documentIterator->current(), $documentIterator->getNextLine());
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
