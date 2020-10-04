<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Builder;

use League\Tactician\CommandBus;
use phpDocumentor\Guides\RestructuredText\Command\ParseFileCommand;
use phpDocumentor\Guides\RestructuredText\Configuration;
use phpDocumentor\Guides\RestructuredText\Kernel;

class ParseQueueProcessor
{
    /** @var CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function process(Kernel $kernel, ParseQueue $parseQueue, string $currentDirectory) : void
    {
        $this->guardThatAnIndexFileExists($currentDirectory, $kernel->getConfiguration());

        foreach ($parseQueue as $file) {
            $this->commandBus->handle(new ParseFileCommand($kernel, $currentDirectory, $file));
        }
    }

    private function guardThatAnIndexFileExists(string $directory, Configuration $configuration): void
    {
        $indexName = $configuration->getNameOfIndexFile();
        $extension = $configuration->getSourceFileExtension();
        $indexFilename = sprintf('%s.%s', $indexName, $extension);
        if (!file_exists($directory . '/' . $indexFilename)) {
            throw new \InvalidArgumentException(sprintf('Could not find index file "%s" in "%s"', $indexFilename, $directory));
        }
    }
}
