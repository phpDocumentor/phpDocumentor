<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

use phpDocumentor\Guides\RestructuredText\Kernel;

final class ParseFileCommand
{
    /** @var Kernel */
    private $kernel;
    private $directory;
    private $file;

    public function __construct(Kernel $kernel, string $directory, string $file)
    {
        $this->kernel = $kernel;
        $this->directory = $directory;
        $this->file = $file;
    }

    public function getKernel(): Kernel
    {
        return $this->kernel;
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
