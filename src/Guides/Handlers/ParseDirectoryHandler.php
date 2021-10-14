<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Handlers;

use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use League\Tactician\CommandBus;
use phpDocumentor\Guides\FileCollector;
use phpDocumentor\Guides\ParseDirectoryCommand;
use phpDocumentor\Guides\ParseFileCommand;
use function sprintf;

final class ParseDirectoryHandler
{
    /** @var FileCollector */
    private $scanner;

    /** @var CommandBus */
    private $commandBus;

    public function __construct(FileCollector $scanner, CommandBus $commandBus)
    {
        $this->scanner = $scanner;
        $this->commandBus = $commandBus;
    }

    public function handle(ParseDirectoryCommand $command): void
    {
        $origin = $command->getOrigin();
        $currentDirectory = $command->getDirectory();
        $documentationSet = $command->getDocumentationSet();
        $extension = $documentationSet->getInputFormat();
        $nameOfIndexFile = 'index';

        $this->guardThatAnIndexFileExists(
            $origin,
            $currentDirectory,
            $nameOfIndexFile,
            $extension
        );

        $parseQueue = $this->scanner->getFiles($origin, $currentDirectory, $extension);
        foreach ($parseQueue as $file) {
            $this->commandBus->handle(
                new ParseFileCommand($documentationSet, $origin, $currentDirectory, $file)
            );
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
