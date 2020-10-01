<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

final class ParseDirectoryCommand
{
    private $directory;
    private $outputDirectory;

    public function __construct(string $directory, string $outputDirectory)
    {
        $this->directory = $directory;
        $this->outputDirectory = $outputDirectory;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function getOutputDirectory(): string
    {
        return $this->outputDirectory;
    }
}
