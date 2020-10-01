<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

use phpDocumentor\Guides\RestructuredText\Builder\Documents;
use phpDocumentor\Guides\RestructuredText\Builder\ParseQueue;
use phpDocumentor\Guides\RestructuredText\Builder\ParseQueueProcessor;
use phpDocumentor\Guides\RestructuredText\Builder\Scanner;
use phpDocumentor\Guides\RestructuredText\Configuration;
use phpDocumentor\Guides\RestructuredText\ErrorManager;
use phpDocumentor\Guides\RestructuredText\Kernel;
use phpDocumentor\Guides\RestructuredText\Meta\CachedMetasLoader;
use phpDocumentor\Guides\RestructuredText\Meta\Metas;

final class ParseDirectoryHandler
{
    /** @var Configuration */
    private $configuration;

    /** @var CachedMetasLoader */
    private $cachedMetasLoader;

    /** @var Metas */
    private $metas;

    // TODO: Isn't this more of a configuration thing?
    /** @var string */
    private $indexName = 'index';

    /** @var Kernel */
    private $kernel;

    /** @var ErrorManager */
    private $errorManager;

    /** @var Documents */
    private $documents;

    public function __construct(
        Kernel $kernel,
        CachedMetasLoader $cachedMetasLoader,
        Metas $metas,
        Documents $documents
    ) {
        $this->kernel = $kernel;
        $this->configuration = $kernel->getConfiguration();
        $this->metas = $metas;
        $this->cachedMetasLoader = $cachedMetasLoader;
        $this->errorManager = new ErrorManager($kernel->getConfiguration());
        $this->documents = $documents;
    }

    public function handle(ParseDirectoryCommand $command)
    {
        $indexFilename = sprintf('%s.%s', $this->indexName, $this->configuration->getSourceFileExtension());
        if (! file_exists($command->getDirectory() . '/' . $indexFilename)) {
            throw new \InvalidArgumentException(sprintf('Could not find index file "%s" in "%s"', $indexFilename, $command->getDirectory()));
        }

        if ($this->configuration->getUseCachedMetas()) {
            $this->cachedMetasLoader->loadCachedMetaEntries($command->getOutputDirectory(), $this->metas);
        }

        $parseQueue = $this->scan($command->getDirectory());

        $this->parse($command->getDirectory(), $command->getOutputDirectory(), $parseQueue);
    }

    private function parse(string $directory, string $targetDirectory, ParseQueue $parseQueue) : void
    {
        $parseQueueProcessor = new ParseQueueProcessor(
            $this->kernel,
            $this->errorManager,
            $this->metas,
            $this->documents,
            $directory,
            $targetDirectory,
            $this->configuration->getFileExtension()
        );

        $parseQueueProcessor->process($parseQueue);
    }

    private function scan(string $directory) : ParseQueue
    {
        $scanner = new Scanner(
            $this->configuration->getSourceFileExtension(),
            $directory,
            $this->metas
        );

        return $scanner->scan();
    }
}
