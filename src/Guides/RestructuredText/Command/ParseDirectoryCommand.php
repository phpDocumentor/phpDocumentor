<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

use phpDocumentor\Guides\RestructuredText\Kernel;

final class ParseDirectoryCommand
{
    /** @var Kernel */
    private $kernel;
    private $directory;

    public function __construct(Kernel $kernel, string $directory)
    {
        $this->kernel = $kernel;
        $this->directory = $directory;
    }

    public function getKernel(): Kernel
    {
        return $this->kernel;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }
}
