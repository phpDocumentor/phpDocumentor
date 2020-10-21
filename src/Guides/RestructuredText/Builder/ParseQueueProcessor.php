<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Builder;

use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use League\Tactician\CommandBus;
use phpDocumentor\Guides\Configuration;
use phpDocumentor\Guides\Files;
use phpDocumentor\Guides\RestructuredText\ParseFileCommand;
use function sprintf;

class ParseQueueProcessor
{
    /** @var CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function process(
        Configuration $configuration,
        Files $parseQueue,
        FilesystemInterface $origin,
        string $currentDirectory
    ) : void {
        $this->guardThatAnIndexFileExists($origin, $currentDirectory, $configuration);

        foreach ($parseQueue as $file) {
            $this->commandBus->handle(new ParseFileCommand($configuration, $origin, $currentDirectory, $file));
        }
    }

    private function guardThatAnIndexFileExists(
        FilesystemInterface $filesystem,
        string $directory,
        Configuration $configuration
    ) : void {
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
