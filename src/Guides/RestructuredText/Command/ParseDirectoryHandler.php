<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

use phpDocumentor\Guides\RestructuredText\Builder\Documents;
use phpDocumentor\Guides\RestructuredText\Builder\ParseQueueProcessor;
use phpDocumentor\Guides\RestructuredText\Builder\Scanner;
use phpDocumentor\Guides\RestructuredText\Meta\Metas;

final class ParseDirectoryHandler
{
    /** @var Metas */
    private $metas;

    // TODO: Isn't this more of a configuration thing?
    /** @var string */
    private $indexName = 'index';

    /** @var Documents */
    private $documents;

    /** @var Scanner */
    private $scanner;

    public function __construct(Metas $metas, Documents $documents, Scanner $scanner)
    {
        $this->metas = $metas;
        $this->documents = $documents;
        $this->scanner = $scanner;
    }

    public function handle(ParseDirectoryCommand $command)
    {
        $kernel = $command->getKernel();
        $extension = $kernel->getConfiguration()->getSourceFileExtension();

        $this->guardThatAnIndexFileExists($command->getDirectory(), $extension);

        $parseQueue = $this->scanner->scan($command->getDirectory(), $extension);

        $parseQueueProcessor = new ParseQueueProcessor(
            $kernel,
            $this->metas,
            $this->documents,
            $command->getDirectory(),
            $command->getOutputDirectory()
        );

        $parseQueueProcessor->process($parseQueue);
    }

    private function guardThatAnIndexFileExists(string $directory, string $extension): void
    {
        $indexFilename = sprintf('%s.%s', $this->indexName, $extension);
        if (!file_exists($directory . '/' . $indexFilename)) {
            throw new \InvalidArgumentException(sprintf('Could not find index file "%s" in "%s"', $indexFilename, $directory));
        }
    }
}
