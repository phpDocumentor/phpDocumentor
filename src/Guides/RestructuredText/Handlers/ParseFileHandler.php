<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Handlers;

use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Guides\Configuration;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Formats\Format;
use phpDocumentor\Guides\Markdown\Parser as MarkdownParser;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Parser as ParserInterface;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\RestructuredText\HTML\HTMLFormat;
use phpDocumentor\Guides\RestructuredText\LaTeX\LaTeXFormat;
use phpDocumentor\Guides\RestructuredText\ParseFileCommand;
use phpDocumentor\Guides\UrlGenerator;
use Psr\Log\LoggerInterface;

use function filemtime;
use function ltrim;
use function sprintf;
use function trim;

final class ParseFileHandler
{
    /** @var Metas */
    private $metas;

    /** @var LoggerInterface */
    private $logger;

    /** @var Renderer */
    private $renderer;

    /** @var UrlGenerator */
    private $urlGenerator;

    /** @var MarkdownParser */
    private $markdownParser;

    /** @var ParserInterface */
    private $rstHtmlParser;

    /** @var ParserInterface */
    private $rstLatexParser;

    public function __construct(
        Metas $metas,
        Renderer $renderer,
        LoggerInterface $logger,
        UrlGenerator $urlGenerator,
        MarkdownParser $markdownParser,
        ParserInterface $rstHtmlParser,
        ParserInterface $rstLatexParser
    ) {
        $this->metas = $metas;
        $this->logger = $logger;
        $this->renderer = $renderer;
        $this->urlGenerator = $urlGenerator;

        $this->markdownParser = $markdownParser;
        $this->rstHtmlParser = $rstHtmlParser;
        $this->rstLatexParser = $rstLatexParser;
    }

    public function handle(ParseFileCommand $command): void
    {
        $configuration = $command->getConfiguration();
        $environment = $this->createEnvironment(
            $configuration,
            $command->getFile(),
            $command->getDirectory(),
            $command->getOrigin()
        );

        $this->logger->info(sprintf('Parsing %s', $environment->getCurrentAbsolutePath()));

        $document = $this->createDocument($configuration, $environment);
        if (!$document) {
            return;
        }

        $this->addDocumentToDocumentationSet($command->getDocumentationSet(), $environment, $document);
        $this->addDocumentToMetas($environment, $configuration, $document);
    }

    private function buildPathOnFileSystem(string $file, string $currentDirectory, string $extension): string
    {
        return ltrim(sprintf('%s/%s.%s', trim($currentDirectory, '/'), $file, $extension), '/');
    }

    private function buildDocumentUrl(Environment $environment, string $extension): string
    {
        return $environment->getUrl() . '.' . $extension;
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

    private function createEnvironment(
        Configuration $configuration,
        string $file,
        string $directory,
        FilesystemInterface $origin
    ): Environment {
        $extension = $configuration->getSourceFileExtension();
        $fileAbsolutePath = $this->buildPathOnFileSystem($file, $directory, $extension);

        $environment = new Environment(
            $configuration->getOutputFolder(),
            $configuration->getInitialHeaderLevel(),
            $this->renderer,
            $this->logger,
            $origin,
            $this->metas,
            $this->urlGenerator
        );
        $environment->setCurrentFileName($file);
        $environment->setCurrentDirectory($directory);
        $environment->setCurrentAbsolutePath($fileAbsolutePath);

        return $environment;
    }

    private function createDocument(Configuration $configuration, Environment $environment): ?DocumentNode
    {
        $path = $environment->getCurrentAbsolutePath();
        $format = $configuration->getFormat();
        $fileExtension = $configuration->getSourceFileExtension();

        // TODO: The NodeRendererFactory on the Environment class is not used as much; refactor that away to remove this
        // runtime state setting
        $environment->setNodeRendererFactory($format->getNodeRendererFactory());

        $parser = $this->determineParser($fileExtension, $format);
        if ($parser instanceof ParserInterface === false) {
            $this->logger->error(
                sprintf('Unable to parse %s, input format was not recognized', $path)
            );

            return null;
        }

        return $parser->parse(
            $environment,
            $this->getFileContents($environment->getOrigin(), $path)
        );
    }

    private function buildOutputUrl(Configuration $configuration, Environment $environment): string
    {
        $outputFolder = $configuration->getOutputFolder() ? $configuration->getOutputFolder() . '/' : '';

        return $outputFolder . $this->buildDocumentUrl($environment, $configuration->getFileExtension());
    }

    private function compileTableOfContents(DocumentNode $document, Environment $environment): array
    {
        $result = [];
        $nodes = $document->getTocs();
        foreach ($nodes as $toc) {
            $files = $toc->getFiles();

            foreach ($files as $key => $file) {
                $files[$key] = $environment->canonicalUrl($file);
            }

            $result[] = $files;
        }

        return $result;
    }

    private function addDocumentToDocumentationSet(
        GuideSetDescriptor $documentationSet,
        Environment $environment,
        DocumentNode $document
    ): void {
        $documentationSet->addDocument(
            $environment->getCurrentFileName(),
            new DocumentDescriptor(
                $document,
                $document->getHash(),
                $environment->getCurrentFileName(),
                $document->getTitle() ? $document->getTitle()->getValueString() : '',
                $document->getTitles(),
                $document->getTocs(),
                $document->getDependencies(),
                $environment->getLinks(),
                $environment->getVariables()
            )
        );
    }

    private function addDocumentToMetas(
        Environment $environment,
        Configuration $configuration,
        DocumentNode  $document
    ): void {
        $this->metas->set(
            $environment->getCurrentFileName(),
            $this->buildOutputUrl($configuration, $environment),
            $document->getTitle() ? $document->getTitle()->getValueString() : '',
            $document->getTitles(),
            $this->compileTableOfContents($document, $environment),
            (int) filemtime($environment->getCurrentAbsolutePath()),
            $document->getDependencies(),
            $environment->getLinks()
        );
    }

    private function determineParser(string $fileExtension, Format $format): ?ParserInterface
    {
        switch (strtolower($fileExtension)) {
            case 'rst':
                if ($format instanceof HTMLFormat) {
                    return $this->rstHtmlParser;
                }
                if ($format instanceof LaTeXFormat) {
                    return $this->rstLatexParser;
                }
                break;

            case 'md':
                return $this->markdownParser;
        }

        return null;
    }
}
