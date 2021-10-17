<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Event;

use Doctrine\Common\EventArgs;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;

final class PreParseDocumentEvent extends EventArgs
{
    public const PRE_PARSE_DOCUMENT = 'preParseDocument';

    /** @var MarkupLanguageParser */
    private $parser;

    /** @var string */
    private $contents;

    public function __construct(MarkupLanguageParser $parser, string $contents)
    {
        $this->parser = $parser;
        $this->contents = $contents;
    }

    public function getParser(): MarkupLanguageParser
    {
        return $this->parser;
    }

    public function setContents(string $contents): void
    {
        $this->contents = $contents;
    }

    public function getContents(): string
    {
        return $this->contents;
    }
}
