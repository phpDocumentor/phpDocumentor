<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use League\Tactician\CommandBus;
use phpDocumentor\Guides\RestructuredText\Command\LoadCacheCommand;
use phpDocumentor\Guides\RestructuredText\Command\ParseDirectoryCommand;
use phpDocumentor\Guides\RestructuredText\Command\PersistCacheCommand;
use phpDocumentor\Guides\RestructuredText\Command\RenderCommand;
use Symfony\Component\Filesystem\Filesystem;
use function is_dir;

class Builder
{
    /** @var Filesystem */
    private $filesystem;

    /** @var CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus, Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->commandBus = $commandBus;
    }

    public function build(Kernel $kernel, string $directory, string $targetDirectory = 'output') : void
    {
        if (! is_dir($targetDirectory)) {
            $this->filesystem->mkdir($targetDirectory, 0755);
        }

        $this->commandBus->handle(new LoadCacheCommand($kernel, $targetDirectory));
        $this->commandBus->handle(new ParseDirectoryCommand($kernel, $directory, $targetDirectory));
        $this->commandBus->handle(new RenderCommand($directory, $targetDirectory));
        $this->commandBus->handle(new PersistCacheCommand($kernel, $targetDirectory));
    }
}
