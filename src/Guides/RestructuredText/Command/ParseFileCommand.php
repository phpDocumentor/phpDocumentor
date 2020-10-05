<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Guides\RestructuredText\Kernel;

final class ParseFileCommand
{
    /** @var Kernel */
    private $kernel;
    private $directory;
    private $file;
    /**
     * @var FilesystemInterface
     */
    private $origin;

    public function __construct(Kernel $kernel, FilesystemInterface $origin, string $directory, string $file)
    {
        $this->kernel = $kernel;
        $this->origin = $origin;
        $this->directory = $directory;
        $this->file = $file;
    }

    public function getKernel(): Kernel
    {
        return $this->kernel;
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
