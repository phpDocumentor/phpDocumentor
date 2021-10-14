<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Handlers;

use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Formats\OutputFormat;
use phpDocumentor\Guides\Formats\OutputFormats;
use phpDocumentor\Guides\Markdown\Parser as MarkdownParser;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\ParseFileCommand;
use phpDocumentor\Guides\Parser as ParserInterface;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\RestructuredText\HTML\HTMLFormat;
use phpDocumentor\Guides\RestructuredText\LaTeX\LaTeXFormat;
use phpDocumentor\Guides\UrlGenerator;
use Psr\Log\LoggerInterface;

use function filemtime;
use function ltrim;
use function sprintf;
use function strtolower;
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

    /** @var OutputFormats */
    private $outputFormats;

    public function __construct(
        Metas $metas,
        Renderer $renderer,
        LoggerInterface $logger,
        UrlGenerator $urlGenerator,
        MarkdownParser $markdownParser,
        ParserInterface $rstHtmlParser,
        ParserInterface $rstLatexParser,
        OutputFormats $outputFormats
    ) {
        $this->metas = $metas;
        $this->logger = $logger;
        $this->renderer = $renderer;
        $this->urlGenerator = $urlGenerator;

        $this->markdownParser = $markdownParser;
        $this->rstHtmlParser = $rstHtmlParser;
        $this->rstLatexParser = $rstLatexParser;
        $this->outputFormats = $outputFormats;
    }

    public function handle(ParseFileCommand $command): void
    {
        $documentationSet = $command->getDocumentationSet();

        $environment = $this->createEnvironment(
            $command->getDirectory(),
            $documentationSet->getInputFormat(),
            $command->getFile(),
            $command->getOrigin(),
            $documentationSet->getOutput(),
            $documentationSet->getInitialHeaderLevel()
        );

        $this->logger->info(sprintf('Parsing %s', $environment->getCurrentAbsolutePath()));

        $document = $this->createDocument($documentationSet, $environment);
        if (!$document) {
            return;
        }

        $this->addDocumentToDocumentationSet($documentationSet, $environment, $document);
        $this->addDocumentToMetas($environment, $documentationSet, $document);
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
        string $sourcePath,
        string $inputFormat,
        string $file,
        FilesystemInterface $origin,
        string $destinationPath,
        int $initialHeaderLevel
    ): Environment {
        $environment = new Environment(
            $destinationPath,
            $initialHeaderLevel,
            $this->renderer,
            $this->logger,
            $origin,
            $this->metas,
            $this->urlGenerator
        );
        $environment->setCurrentFileName($file);
        $environment->setCurrentDirectory($sourcePath);
        $environment->setCurrentAbsolutePath($this->buildPathOnFileSystem($file, $sourcePath, $inputFormat));

        return $environment;
    }

    private function createDocument(GuideSetDescriptor $guideSetDescriptor, Environment $environment): ?DocumentNode
    {
        $path = $environment->getCurrentAbsolutePath();
        $format = $this->outputFormats->get($guideSetDescriptor->getOutputFormat());

        // TODO: The NodeRendererFactory on the Environment class is not used as much; refactor that away to remove this
        // runtime state setting
        $environment->setNodeRendererFactory($format->getNodeRendererFactory());

        $parser = $this->determineParser($guideSetDescriptor->getInputFormat(), $format);
        if ($parser instanceof ParserInterface === false) {
            $this->logger->error(
                sprintf('Unable to parse %s, input format was not recognized', $path)
            );

            return null;
        }

        $environment->reset();

        return $parser->parse(
            $environment,
            $this->getFileContents($environment->getOrigin(), $path)
        );
    }

    private function buildOutputUrl(GuideSetDescriptor $guideSetDescriptor, Environment $environment): string
    {
        $outputFolder = $guideSetDescriptor->getOutput() ? $guideSetDescriptor->getOutput() . '/' : '';

        return $outputFolder . $this->buildDocumentUrl($environment, $guideSetDescriptor->getOutputFormat());
    }

    /**
     * @return array<array<string|null>>
     */
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
        GuideSetDescriptor $documentationSet,
        DocumentNode $document
    ): void {
        $this->metas->set(
            $environment->getCurrentFileName(),
            $this->buildOutputUrl($documentationSet, $environment),
            $document->getTitle() ? $document->getTitle()->getValueString() : '',
            $document->getTitles(),
            $this->compileTableOfContents($document, $environment),
            (int) filemtime($environment->getCurrentAbsolutePath()),
            $document->getDependencies(),
            $environment->getLinks()
        );
    }

    private function determineParser(string $fileExtension, OutputFormat $format): ?ParserInterface
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
