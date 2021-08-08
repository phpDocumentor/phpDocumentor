<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Handlers;

use Doctrine\Common\EventManager;
use InvalidArgumentException;
use IteratorAggregate;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Guides\Configuration;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Markdown\Parser as MarkdownParser;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\References\Reference;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Formats\Format;
use phpDocumentor\Guides\RestructuredText\ParseFileCommand;
use phpDocumentor\Guides\RestructuredText\Parser;
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

    /**
     * @param IteratorAggregate<Directive> $directives
     * @param IteratorAggregate<Reference> $references
     */
    public function __construct(
        Metas $metas,
        Renderer $renderer,
        LoggerInterface $logger,
        EventManager $eventManager,
        IteratorAggregate $directives,
        IteratorAggregate $references
    ) {
        $this->metas = $metas;
        $this->logger = $logger;
        $this->directives = $directives;
        $this->references = $references;
        $this->eventManager = $eventManager;
        $this->renderer = $renderer;
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
            $this->metas
        );
        $environment->setCurrentFileName($file);
        $environment->setCurrentDirectory($directory);

        $fileAbsolutePath = $this->buildPathOnFileSystem(
            $file,
            $directory,
            $configuration->getSourceFileExtension()
        );

        $this->logger->info(sprintf('Parsing %s', $fileAbsolutePath));
        $document = null;

        if ($configuration->getSourceFileExtension() === 'rst') {
            $document = $this->parseRestructuredText($configuration, $environment, $fileAbsolutePath);
        }

        if ($configuration->getSourceFileExtension() === 'md') {
            $document = $this->parseMarkdown($configuration, $environment, $fileAbsolutePath);
        }

        if (!$document) {
            $this->logger->error(sprintf('Unable to parse %s, input format was not recognized', $fileAbsolutePath));

            return;
        }

        $command->getDocumentationSet()->addDocument(
            $file,
            new DocumentDescriptor(
                $document,
                $document->getHash(),
                $file,
                $document->getTitle()->getValueString(),
                $document->getTitles(),
                $document->getTocs(),
                $environment->getDependencies(),
                $environment->getLinks()
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
            $document->getTitle()->getValueString(),
            $document->getTitles(),
            $tocs,
            (int) filemtime($fileAbsolutePath),
            $environment->getDependencies(),
            $environment->getLinks()
        );
    }

    private function buildPathOnFileSystem(string $file, string $currentDirectory, string $extension): string
    {
        return ltrim(trim($currentDirectory, '/') . '/' . $file . '.' . $extension, '/');
    }

    private function buildDocumentUrl(Environment $environment, string $extension): string
    {
        return $environment->getUrl() . '.' . $extension;
    }

    private function parseRestructuredText(
        Configuration $configuration,
        Environment $environment,
        string $fileAbsolutePath
    ): DocumentNode {
        $format = $configuration->getFormat();
        if ($format instanceof Format === false) {
            throw new RuntimeException('This handler only support RestructuredText input formats');
        }

        $nodeRendererFactory = $format->getNodeRendererFactory($environment);
        $environment->setNodeRendererFactory($nodeRendererFactory);

        $parser = new Parser(
            $format,
            $environment,
            $this->eventManager,
            iterator_to_array($this->directives),
            iterator_to_array($this->references)
        );

        return $parser->parse($this->getFileContents($environment, $fileAbsolutePath));
    }

    private function parseMarkdown(
        Configuration $configuration,
        Environment $environment,
        string $fileAbsolutePath
    ): DocumentNode {
        $nodeRendererFactory = $configuration->getFormat()->getNodeRendererFactory($environment);
        $environment->setNodeRendererFactory($nodeRendererFactory);

        $parser = new MarkdownParser($environment);

        return $parser->parse($this->getFileContents($environment, $fileAbsolutePath));
    }

    private function getFileContents(Environment $environment, string $file): string
    {
        $origin = $environment->getOrigin();
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
