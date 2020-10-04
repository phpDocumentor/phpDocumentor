<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Builder;

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

    /** @var string */
    private $directory;

    /** @var string */
    private $targetDirectory;

    public function __construct(
        Kernel $kernel,
        Metas $metas,
        Documents $documents,
        string $directory,
        string $targetDirectory
    ) {
        $this->kernel          = $kernel;
        $this->metas           = $metas;
        $this->documents       = $documents;
        $this->directory       = $directory;
        $this->targetDirectory = $targetDirectory;
    }

    public function process(ParseQueue $parseQueue) : void
    {
        foreach ($parseQueue->getAllFilesThatRequireParsing() as $file) {
            $this->processFile($file);
        }
    }

    private function processFile(string $file) : void
    {
        $fileAbsolutePath = $this->buildFileAbsolutePath($file);

        $parser = $this->createFileParser($file);

        $environment = $parser->getEnvironment();

        $document = $parser->parseFile($fileAbsolutePath);

        $this->documents->addDocument($file, $document);

        $this->kernel->postParse($document);

        $this->metas->set(
            $file,
            $this->buildDocumentUrl($document),
            (string) $document->getTitle(),
            $document->getTitles(),
            $document->getTocs(),
            (int) filemtime($fileAbsolutePath),
            $environment->getDependencies(),
            $environment->getLinks()
        );
    }

    private function createFileParser(string $file) : Parser
    {
        $parser = new Parser($this->kernel, new Environment($this->kernel->getConfiguration(), $this->kernel->getLogger()));

        $environment = $parser->getEnvironment();
        $environment->setMetas($this->metas);
        $environment->setCurrentFileName($file);
        $environment->setCurrentDirectory($this->directory);
        $environment->setTargetDirectory($this->targetDirectory);

        return $parser;
    }

    private function buildFileAbsolutePath(string $file) : string
    {
        return $this->directory . '/' . $file . '.rst';
    }

    private function buildDocumentUrl(DocumentNode $document) : string
    {
        $fileExtension = $this->kernel->getConfiguration()->getFileExtension();

        return $document->getEnvironment()->getUrl() . '.' . $fileExtension;
    }
}
