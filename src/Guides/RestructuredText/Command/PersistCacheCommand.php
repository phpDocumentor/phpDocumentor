<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

use phpDocumentor\Guides\RestructuredText\Kernel;

final class PersistCacheCommand
{
    /** @var Kernel */
    private $kernel;

    private $outputDirectory;

    public function __construct(Kernel $kernel, string $outputDirectory)
    {
        $this->kernel = $kernel;
        $this->outputDirectory = $outputDirectory;
    }

    public function getKernel(): Kernel
    {
        return $this->kernel;
    }

    public function getOutputDirectory(): string
    {
        return $this->outputDirectory;
    }
}
