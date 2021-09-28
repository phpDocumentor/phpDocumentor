<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

use function preg_match;

class LineChecker
{
    /** @var LineDataParser */
    private $lineParser;

    public function __construct(LineDataParser $lineParser)
    {
        $this->lineParser = $lineParser;
    }

    public function isListLine(string $line, bool $isCode): bool
    {
        $listLine = $this->lineParser->parseListLine($line);

        if ($listLine !== null) {
            return $listLine->getDepth() === 0 || !$isCode;
        }

        return false;
    }

    public function isDirective(string $line): bool
    {
        return preg_match('/^\.\. (\|(.+)\| |)([^\s]+)::( (.*)|)$/mUsi', $line) > 0;
    }
}
