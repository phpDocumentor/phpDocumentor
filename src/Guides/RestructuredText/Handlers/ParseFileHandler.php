<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Handlers;

use Doctrine\Common\EventManager;
use InvalidArgumentException;
use IteratorAggregate;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Formats\Format;
use phpDocumentor\Guides\Markdown\Parser as MarkdownParser;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\ReferenceRegistry;
use phpDocumentor\Guides\References\Reference;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Formats\Format as RestructuredTextFormat;
use phpDocumentor\Guides\RestructuredText\ParseFileCommand;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\UrlGenerator;
use Psr\Log\LoggerInterface;

use RuntimeException;
use function filemtime;
use function iterator_to_array;
use function ltrim;
use function sprintf;
use function trim;

final class ParseFileHandler
{
    /** @var Metas */
    private $metas;

    /** @var LoggerInterface */
    private $logger;

    /** @var IteratorAggregate<Directive> */
    private $directives;

    /** @var IteratorAggregate<Reference> */
    private $references;

    /** @var EventManager */
    private $eventManager;

    /** @var Renderer */
    private $renderer;

    /** @var UrlGenerator */
    private $urlGenerator;

    /**
     * @param IteratorAggregate<Directive> $directives
     * @param IteratorAggregate<Reference> $references
     */
    public function __construct(
        Metas $metas,
        Renderer $renderer,
        LoggerInterface $logger,
        EventManager $eventManager,
        UrlGenerator $urlGenerator,
        IteratorAggregate $directives,
        IteratorAggregate $references
    ) {
        $this->metas = $metas;
        $this->logger = $logger;
        $this->directives = $directives;
        $this->references = $references;
        $this->eventManager = $eventManager;
        $this->renderer = $renderer;
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(ParseFileCommand $command): void
    {
        $configuration = $command->getConfiguration();
        $directory = $command->getDirectory();
        $file = $command->getFile();

        $environment = new Environment(
            $configuration,
            $this->renderer,
            $this->logger,
            $command->getOrigin(),
            $this->metas,
            $this->urlGenerator
        );
        $fileAbsolutePath = $this->buildPathOnFileSystem(
            $file,
            $directory,
            $configuration->getSourceFileExtension()
        );
        $environment->setCurrentFileName($file);
        $environment->setCurrentAbsolutePath($fileAbsolutePath);
        $environment->setCurrentDirectory($directory);

        $this->logger->info(sprintf('Parsing %s', $fileAbsolutePath));
        $document = null;

        $referenceRegistry = new ReferenceRegistry($this->logger, $this->urlGenerator);
        if ($configuration->getSourceFileExtension() === 'rst') {
            $document = $this->parseRestructuredText(
                $configuration->getFormat(),
                $referenceRegistry,
                $environment,
                $fileAbsolutePath
            );
        }

        if ($configuration->getSourceFileExtension() === 'md') {
            $document = $this->parseMarkdown(
                $configuration->getFormat(),
                $referenceRegistry,
                $environment,
                $fileAbsolutePath
            );
        }

        if (!$document) {
            $this->logger->error(sprintf('Unable to parse %s, input format was not recognized', $fileAbsolutePath));

            return;
        }

        $title = $document->getTitle() ? $document->getTitle()->getValueString() : '';

        $command->getDocumentationSet()->addDocument(
            $file,
            new DocumentDescriptor(
                $document,
                $document->getHash(),
                $file,
                $title,
                $document->getTitles(),
                $document->getTocs(),
                $referenceRegistry->getDependencies(),
                $environment->getLinks(),
                $environment->getVariables()
            )
        );

        $outputFolder = $configuration->getOutputFolder() ? $configuration->getOutputFolder() . '/' : '';
        $url = $outputFolder . $this->buildDocumentUrl($environment, $configuration->getFileExtension());

        $tocs = [];
        $nodes = $document->getTocs();
        foreach ($nodes as $toc) {
            $files = $toc->getFiles();

            foreach ($files as &$filea) {
                $filea = $environment->canonicalUrl($filea);
            }

            $tocs[] = $files;
        }

        $this->metas->set(
            $file,
            $url,
            $title,
            $document->getTitles(),
            $tocs,
            (int) filemtime($fileAbsolutePath),
            $referenceRegistry->getDependencies(),
            $environment->getLinks()
        );
    }

    private function buildPathOnFileSystem(string $file, string $currentDirectory, string $extension): string
    {
        return ltrim(sprintf('%s/%s.%s', trim($currentDirectory, '/'), $file, $extension), '/');
    }

    private function buildDocumentUrl(Environment $environment, string $extension): string
    {
        return $environment->getUrl() . '.' . $extension;
    }

    private function parseRestructuredText(
        Format $format,
        ReferenceRegistry $referenceRegistry,
        Environment $environment,
        string $fileAbsolutePath
    ): DocumentNode {
        if ($format instanceof RestructuredTextFormat === false) {
            throw new RuntimeException('This handler only support RestructuredText input formats');
        }

        $nodeRendererFactory = $format->getNodeRendererFactory($referenceRegistry);
        $environment->setNodeRendererFactory($nodeRendererFactory);

        $parser = new Parser(
            $format,
            $referenceRegistry,
            $this->eventManager,
            iterator_to_array($this->directives),
            iterator_to_array($this->references)
        );

        return $parser->parse($environment, $this->getFileContents($environment->getOrigin(), $fileAbsolutePath));
    }

    private function parseMarkdown(
        Format $format,
        ReferenceRegistry $referenceRegistry,
        Environment $environment,
        string $fileAbsolutePath
    ): DocumentNode {
        $nodeRendererFactory = $format->getNodeRendererFactory($referenceRegistry);
        $environment->setNodeRendererFactory($nodeRendererFactory);

        $parser = new MarkdownParser();

        return $parser->parse($environment, $this->getFileContents($environment->getOrigin(), $fileAbsolutePath));
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
}
