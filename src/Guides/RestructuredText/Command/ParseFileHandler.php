<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

use phpDocumentor\Guides\RestructuredText\Builder\Documents;
use phpDocumentor\Guides\RestructuredText\Builder\ParseQueueProcessor;
use phpDocumentor\Guides\RestructuredText\Builder\Scanner;
use phpDocumentor\Guides\RestructuredText\Configuration;
use phpDocumentor\Guides\RestructuredText\Environment;
use phpDocumentor\Guides\RestructuredText\Meta\Metas;
use phpDocumentor\Guides\RestructuredText\Nodes\DocumentNode;
use phpDocumentor\Guides\RestructuredText\Parser;

final class ParseFileHandler
{
    /** @var Metas */
    private $metas;

    /** @var Documents */
    private $documents;

    public function __construct(Metas $metas, Documents $documents)
    {
        $this->metas = $metas;
        $this->documents = $documents;
    }

    public function handle(ParseFileCommand $command): void
    {
        $kernel = $command->getKernel();
        $configuration = $kernel->getConfiguration();

        $environment = new Environment($configuration, $kernel->getLogger());
        $environment->setMetas($this->metas);
        $environment->setCurrentFileName($command->getFile());
        $environment->setCurrentDirectory($command->getDirectory());

        $parser = new Parser($kernel, $environment);

        $fileAbsolutePath = $this->buildFileAbsolutePath(
            $command->getFile(),
            $command->getDirectory(),
            $configuration->getSourceFileExtension()
        );
        $document = $parser->parseFile($fileAbsolutePath);

        $this->documents->addDocument($command->getFile(), $document);

        $kernel->postParse($document);

        $this->metas->set(
            $command->getFile(),
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
