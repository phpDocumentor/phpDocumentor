<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use League\Tactician\CommandBus;
use League\Tactician\Setup\QuickStart;
use phpDocumentor\Guides\RestructuredText\Command\ParseDirectoryCommand;
use phpDocumentor\Guides\RestructuredText\Command\ParseDirectoryHandler;
use phpDocumentor\Guides\RestructuredText\Command\RenderCommand;
use phpDocumentor\Guides\RestructuredText\Command\RenderHandler;
use phpDocumentor\Guides\RestructuredText\Meta\CachedMetasLoader;
use phpDocumentor\Guides\RestructuredText\Meta\Metas;
use Symfony\Component\Filesystem\Filesystem;
use function is_dir;

class Builder
{
    /** @var Filesystem */
    private $filesystem;

    /** @var CommandBus */
    private $commandBus;

    public function __construct(Kernel $kernel)
    {
        $metas = new Metas();
        $cachedMetasLoader = new CachedMetasLoader();
        $this->filesystem = new Filesystem();

        $documents = new Builder\Documents($this->filesystem, $metas);

        $this->commandBus = QuickStart::create(
            [
                ParseDirectoryCommand::class => new ParseDirectoryHandler(
                    $kernel,
                    $cachedMetasLoader,
                    $metas,
                    $documents
                ),
                RenderCommand::class => new RenderHandler(
                    $documents,
                    $this->filesystem,
                    $metas,
                    $cachedMetasLoader
                )
            ]
        );
    }

    public function build(string $directory, string $targetDirectory = 'output') : void
    {
        if (! is_dir($targetDirectory)) {
            $this->filesystem->mkdir($targetDirectory, 0755);
        }

        $this->commandBus->handle(new ParseDirectoryCommand($directory, $targetDirectory));
        $this->commandBus->handle(new RenderCommand($directory, $targetDirectory));
    }
}
