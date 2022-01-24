<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\Handlers;

use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Guides\Event\PostParseDocument;
use phpDocumentor\Guides\Event\PreParseDocument;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Parser;
use Psr\EventDispatcher\EventDispatcherInterface;
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

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        Metas $metas,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher,
        Parser $parser
    ) {
        $this->metas = $metas;
        $this->logger = $logger;
        $this->parser = $parser;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function handle(ParseFileCommand $command): ?DocumentNode
    {
        $this->logger->info(sprintf('Parsing %s', $command->getFile()));

        return $this->createDocument(
            $command->getOrigin(),
            $command->getDirectory(),
            $command->getFile(),
            $command->getExtension(),
            $command->getInitialHeaderLevel()
        );
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
        FilesystemInterface $origin,
        string $documentFolder,
        string $fileName,
        string $extension,
        int $initialHeaderLevel
    ): ?DocumentNode {
        $path = $this->buildPathOnFileSystem($fileName, $documentFolder, $extension);
        $fileContents = $this->getFileContents($origin, $path);

        $this->parser->prepare(
            $this->metas,
            $origin,
            $documentFolder,
            $fileName,
            $initialHeaderLevel
        );

        $preParseDocumentEvent = $this->eventDispatcher->dispatch(
            new PreParseDocument($this->parser, $path, $fileContents)
        );

        $document = null;
        try {
            $document = $this->parser->parse($preParseDocumentEvent->getContents(), $extension);
        } catch (RuntimeException $e) {
            $this->logger->error(
                sprintf('Unable to parse %s, input format was not recognized', $path)
            );
        }

        $this->eventDispatcher->dispatch(new PostParseDocument($fileName, $document));

        return $document;
    }

    private function buildPathOnFileSystem(string $file, string $currentDirectory, string $extension): string
    {
        return ltrim(sprintf('%s/%s.%s', trim($currentDirectory, '/'), $file, $extension), '/');
    }
}
