<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Handlers;

use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use League\Tactician\CommandBus;
use phpDocumentor\Guides\Configuration;
use phpDocumentor\Guides\FileCollector;
use phpDocumentor\Guides\RestructuredText\ParseDirectoryCommand;
use phpDocumentor\Guides\RestructuredText\ParseFileCommand;

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
        $configuration = $command->getConfiguration();

        $extension = $configuration->getSourceFileExtension();
        $parseQueue = $this->scanner->getFiles($command->getOrigin(), $command->getDirectory(), $extension);

        $origin = $command->getOrigin();
        $currentDirectory = $command->getDirectory();
        $this->guardThatAnIndexFileExists($origin, $currentDirectory, $configuration);

        foreach ($parseQueue as $file) {
            $this->commandBus->handle(
                new ParseFileCommand($command->getDocumentationSet(), $configuration, $origin, $currentDirectory, $file)
            );
        }
    }

    private function guardThatAnIndexFileExists(
        FilesystemInterface $filesystem,
        string $directory,
        Configuration $configuration
    ): void {
        $indexName = $configuration->getNameOfIndexFile();
        $extension = $configuration->getSourceFileExtension();
        $indexFilename = sprintf('%s.%s', $indexName, $extension);
        if (!$filesystem->has($directory . '/' . $indexFilename)) {
            throw new InvalidArgumentException(
                sprintf('Could not find index file "%s" in "%s"', $indexFilename, $directory)
            );
        }
    }
}
