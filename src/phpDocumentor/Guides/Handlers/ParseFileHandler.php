<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Handlers;

use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Guides\Meta\Entry;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\ParseFileCommand;
use phpDocumentor\Guides\Parser;
use Psr\Log\LoggerInterface;
use RuntimeException;

use function ltrim;
use function sprintf;
use function trim;

final class ParseFileHandler
{
    /** @var Metas */
    private $metas;

    /** @var LoggerInterface */
    private $logger;

    /** @var Parser */
    private $parser;

    public function __construct(
        Metas $metas,
        LoggerInterface $logger,
        Parser $parser
    ) {
        $this->metas = $metas;
        $this->logger = $logger;
        $this->parser = $parser;
    }

    public function handle(ParseFileCommand $command): void
    {
        $this->logger->info(sprintf('Parsing %s', $command->getFile()));

        $document = $this->createDocument(
            $command->getDocumentationSet(),
            $command->getOrigin(),
            $command->getDirectory(),
            $command->getFile()
        );
        if ($document instanceof DocumentNode === false) {
            return;
        }

        $this->addDocumentToDocumentationSet($command->getDocumentationSet(), $command->getFile(), $document);
    }

    private function buildPathOnFileSystem(string $file, string $currentDirectory, string $extension): string
    {
        return ltrim(sprintf('%s/%s.%s', trim($currentDirectory, '/'), $file, $extension), '/');
    }

    private function getFileContents(FilesystemInterface $origin, string $file): string
    {
        if (!$origin->has($file)) {
            throw new InvalidArgumentException(sprintf('File at path %s does not exist', $file));
        }

        $contents = $origin->read($file);

        if ($contents === false) {
            throw new InvalidArgumentException(sprintf('Could not load file from path %s', $file));
        }

        return $contents;
    }

    private function createDocument(
        GuideSetDescriptor $documentationSet,
        FilesystemInterface $origin,
        string $documentFolder,
        string $fileName
    ): ?DocumentNode {
        $path = $this->buildPathOnFileSystem($fileName, $documentFolder, $documentationSet->getInputFormat());
        $fileContents = $this->getFileContents($origin, $path);

        $this->parser->prepare(
            $this->metas,
            $origin,
            $documentFolder,
            $documentationSet->getOutputLocation(),
            $fileName,
            $documentationSet->getInitialHeaderLevel()
        );

        $document = null;
        try {
            $document = $this->parser->parse(
                $fileContents,
                $documentationSet->getInputFormat()
            );
        } catch (RuntimeException $e) {
            $this->logger->error(
                sprintf('Unable to parse %s, input format was not recognized', $path)
            );
        }

        return $document;
    }

    private function addDocumentToDocumentationSet(
        GuideSetDescriptor $documentationSet,
        string $file,
        DocumentNode $document
    ): void {
        $metaEntry = $this->metas->get($file);
        if ($metaEntry instanceof Entry === false) {
            $this->logger->error(sprintf('Could not find meta entry for %s, parsing may have failed', $file));

            return;
        }

        $documentationSet->addDocument(
            $file,
            new DocumentDescriptor(
                $document,
                $document->getHash(),
                $file,
                $document->getTitle() ? $document->getTitle()->getValueString() : '',
                $document->getTitles(),
                $document->getTocs(),
                $document->getDependencies(),
                $metaEntry->getLinks(),
                $document->getVariables()
            )
        );
    }
}
