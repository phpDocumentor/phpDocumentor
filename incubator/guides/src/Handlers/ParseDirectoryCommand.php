<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Handlers;

use League\Flysystem\FilesystemInterface;

final class ParseDirectoryCommand
{
    /** @var FilesystemInterface */
    private $origin;

    /** @var string */
    private $directory;

    private string $inputFormat;

    public function __construct(
        FilesystemInterface $origin,
        string $directory,
        string $inputFormat
    ) {
        $this->origin = $origin;
        $this->directory = $directory;
        $this->inputFormat = $inputFormat;
    }

    public function getOrigin(): FilesystemInterface
    {
        return $this->origin;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function getInputFormat(): string
    {
        return $this->inputFormat;
    }
}
