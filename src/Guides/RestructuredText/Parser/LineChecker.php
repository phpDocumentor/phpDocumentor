<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

use function in_array;
use function preg_match;
use function strlen;
use function strpos;
use function trim;

class LineChecker
{
    private const HEADER_LETTERS = ['=', '-', '~', '*', '+', '^', '"', '.', '`', "'", '_', '#', ':'];

    /** @var LineDataParser */
    private $lineParser;

    public function __construct(LineDataParser $lineParser)
    {
        $this->lineParser = $lineParser;
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

    public function isListLine(string $line, bool $isCode): bool
    {
        $listLine = $this->lineParser->parseListLine($line);

        if ($listLine !== null) {
            return $listLine->getDepth() === 0 || !$isCode;
        }

        return false;
    }

    public function isBlockLine(string $line): bool
    {
        if ($line !== '') {
            return trim($line[0]) === '';
        }

        return trim($line) === '';
    }

    public function isComment(string $line): bool
    {
        return preg_match('/^\.\. (.*)$/mUsi', $line) > 0;
    }

    public function isDirective(string $line): bool
    {
        return preg_match('/^\.\. (\|(.+)\| |)([^\s]+)::( (.*)|)$/mUsi', $line) > 0;
    }

    public function isDefinitionList(string $line): bool
    {
        return strpos($line, '    ') === 0;
    }

    public function isDefinitionListEnded(string $line, string $nextLine): bool
    {
        if (trim($line) === '') {
            return false;
        }

        if ($this->isDefinitionList($line)) {
            return false;
        }

        return !$this->isDefinitionList($nextLine);
    }
}
