<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Builder;

use phpDocumentor\Guides\RestructuredText\Configuration;
use phpDocumentor\Guides\RestructuredText\Environment;
use phpDocumentor\Guides\RestructuredText\Kernel;
use phpDocumentor\Guides\RestructuredText\Meta\Metas;
use phpDocumentor\Guides\RestructuredText\Nodes\DocumentNode;
use phpDocumentor\Guides\RestructuredText\Parser;
use function filemtime;

class ParseQueueProcessor
{
    /** @var Kernel */
    private $kernel;

    /** @var Metas */
    private $metas;

    /** @var Documents */
    private $documents;

    public function __construct(Metas $metas, Documents $documents)
    {
        $this->metas = $metas;
        $this->documents = $documents;
    }

    public function process(Kernel $kernel, ParseQueue $parseQueue, string $currentDirectory) : void
    {
        $this->kernel = $kernel;
        $this->guardThatAnIndexFileExists($currentDirectory, $kernel->getConfiguration());

        foreach ($parseQueue as $file) {
            $this->processFile($file, $currentDirectory);
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

    private function processFile(string $file, string $currentDirectory) : void
    {
        $configuration = $this->kernel->getConfiguration();

        $environment = new Environment($configuration, $this->kernel->getLogger());
        $environment->setMetas($this->metas);
        $environment->setCurrentFileName($file);
        $environment->setCurrentDirectory($currentDirectory);

        $parser = new Parser($this->kernel, $environment);

        $fileAbsolutePath = $this->buildFileAbsolutePath($file, $currentDirectory, $configuration->getSourceFileExtension());
        $document = $parser->parseFile($fileAbsolutePath);

        $this->documents->addDocument($file, $document);

        $this->kernel->postParse($document);

        $this->metas->set(
            $file,
            $this->buildDocumentUrl($document, $configuration->getFileExtension()),
            (string) $document->getTitle(),
            $document->getTitles(),
            $document->getTocs(),
            (int) filemtime($fileAbsolutePath),
            $environment->getDependencies(),
            $environment->getLinks()
        );
    }

    private function buildFileAbsolutePath(string $file, string $currentDirectory, string $extension) : string
    {
        return $currentDirectory . '/' . $file . '.' . $extension;
    }

    private function buildDocumentUrl(DocumentNode $document, string $extension) : string
    {
        return $document->getEnvironment()->getUrl() . '.' . $extension;
    }
}
