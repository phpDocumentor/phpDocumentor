<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

use League\Flysystem\FilesystemInterface;

final class RenderCommand
{
    /** @var FilesystemInterface */
    private $filesystem;

    private $outputDirectory;

    public function __construct(FilesystemInterface $filesystem, string $outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;
        $this->filesystem = $filesystem;
    }

    public function getDestination(): FilesystemInterface
    {
        return $this->filesystem;
    }

    public function getOutputDirectory(): string
    {
        return $this->outputDirectory;
    }
}
