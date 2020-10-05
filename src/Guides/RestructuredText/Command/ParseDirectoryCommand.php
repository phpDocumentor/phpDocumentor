<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Guides\RestructuredText\Kernel;

final class ParseDirectoryCommand
{
    /** @var Kernel */
    private $kernel;

    /** @var FilesystemInterface */
    private $origin;

    /** @var string  */
    private $directory;

    public function __construct(Kernel $kernel, FilesystemInterface $origin, string $directory)
    {
        $this->kernel = $kernel;
        $this->origin = $origin;
        $this->directory = $directory;
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
}
