<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Guides\Configuration;

final class ParseFileCommand
{
    /** @var Configuration */
    private $configuration;
    private $directory;
    private $file;
    /**
     * @var FilesystemInterface
     */
    private $origin;

    public function __construct(Configuration $configuration, FilesystemInterface $origin, string $directory, string $file)
    {
        $this->configuration = $configuration;
        $this->origin = $origin;
        $this->directory = $directory;
        $this->file = $file;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function getOrigin(): FilesystemInterface
    {
        return $this->origin;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function getFile(): string
    {
        return $this->file;
    }
}
