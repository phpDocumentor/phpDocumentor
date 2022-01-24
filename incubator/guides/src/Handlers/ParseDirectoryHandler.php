<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Handlers;

use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use League\Tactician\CommandBus;
use phpDocumentor\Guides\FileCollector;

use function sprintf;

final class ParseDirectoryHandler
{
    private FileCollector $fileCollector;
    private CommandBus $commandBus;

    public function __construct(FileCollector $scanner, CommandBus $commandBus)
    {
        $this->fileCollector = $scanner;
        $this->commandBus = $commandBus;
    }

    public function handle(ParseDirectoryCommand $command): void
    {
        $origin = $command->getOrigin();
        $currentDirectory = $command->getDirectory();
        $extension = $command->getInputFormat();
        $nameOfIndexFile = 'index';

        $this->guardThatAnIndexFileExists(
            $origin,
            $currentDirectory,
            $nameOfIndexFile,
            $extension
        );

        $files = $this->fileCollector->collect($origin, $currentDirectory, $extension);
        foreach ($files as $file) {
            $this->commandBus->handle(new ParseFileCommand($origin, $currentDirectory, $file, $extension, 1));
        }
    }

    private function guardThatAnIndexFileExists(
        FilesystemInterface $filesystem,
        string $directory,
        string $nameOfIndexFile,
        string $sourceFormat
    ): void {
        $indexName = $nameOfIndexFile;
        $extension = $sourceFormat;
        $indexFilename = sprintf('%s.%s', $indexName, $extension);
        if (!$filesystem->has($directory . '/' . $indexFilename)) {
            throw new InvalidArgumentException(
                sprintf('Could not find index file "%s" in "%s"', $indexFilename, $directory)
            );
        }
    }
}
