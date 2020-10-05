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
use Psr\Log\LoggerInterface;

final class ParseFileHandler
{
    /** @var Metas */
    private $metas;

    /** @var Documents */
    private $documents;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(Metas $metas, Documents $documents, LoggerInterface $logger)
    {
        $this->metas = $metas;
        $this->documents = $documents;
        $this->logger = $logger;
    }

    public function handle(ParseFileCommand $command): void
    {
        $kernel = $command->getKernel();
        $file = $command->getFile();
        $configuration = $kernel->getConfiguration();

        $environment = new Environment($configuration, $kernel->getLogger(), $command->getOrigin());
        $environment->setMetas($this->metas);
        $environment->setCurrentFileName($file);
        $environment->setCurrentDirectory($command->getDirectory());

        $parser = new Parser($kernel, $environment);

        $fileAbsolutePath = $this->buildPathOnFileSystem(
            $file,
            $command->getDirectory(),
            $configuration->getSourceFileExtension()
        );

        $this->logger->info(sprintf('Parsing %s', $fileAbsolutePath));
        $document = $parser->parseFile($fileAbsolutePath);

        $this->documents->addDocument($file, $document);

        $kernel->postParse($document);

        $url = $this->buildDocumentUrl($document, $configuration->getFileExtension());
        $this->metas->set(
            $file,
            $url,
            (string) $document->getTitle(),
            $document->getTitles(),
            $document->getTocs(),
            (int) filemtime($fileAbsolutePath),
            $environment->getDependencies(),
            $environment->getLinks()
        );
    }

    private function buildPathOnFileSystem(string $file, string $currentDirectory, string $extension) : string
    {
        return ltrim(trim($currentDirectory, '/') . '/' . $file . '.' . $extension, '/');
    }

    private function buildDocumentUrl(DocumentNode $document, string $extension) : string
    {
        return $document->getEnvironment()->getUrl() . '.' . $extension;
    }
}
