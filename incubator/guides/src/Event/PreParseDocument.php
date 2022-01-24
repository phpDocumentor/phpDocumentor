<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Event;

use phpDocumentor\Guides\Parser;

final class PreParseDocument
{
    private Parser $parser;
    private string $contents;
    private string $fileName;

    public function __construct(Parser $parser, string $fileName, string $contents)
    {
        $this->parser = $parser;
        $this->contents = $contents;
        $this->fileName = $fileName;
    }

    public function getParser(): Parser
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

    public function getFileName(): string
    {
        return $this->fileName;
    }
}
