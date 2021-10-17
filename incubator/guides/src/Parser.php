<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Guides\Formats\OutputFormat;
use phpDocumentor\Guides\Formats\OutputFormats;
use phpDocumentor\Guides\Nodes\DocumentNode;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;

use function filemtime;
use function getcwd;
use function ltrim;
use function sprintf;
use function trim;

/**
 * Determines the correct markup language parser to use based on the input and output format and with it, and parses
 * the file contents.
 */
final class Parser
{
    /** @var ?Environment */
    private $environment = null;

    /** @var ?Metas */
    private $metas = null;

    /** @var LoggerInterface */
    private $logger;

    /** @var UrlGenerator */
    private $urlGenerator;

    /** @var OutputFormats */
    private $outputFormats;

    /** @var MarkupLanguageParser[] */
    private $parserStrategies = [];

    /**
     * @param iterable<MarkupLanguageParser> $parserStrategies
     */
    public function __construct(
        UrlGenerator $urlGenerator,
        OutputFormats $outputFormats,
        iterable $parserStrategies,
        ?LoggerInterface $logger = null
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->outputFormats = $outputFormats;
        $this->logger = $logger ?? new NullLogger();

        foreach ($parserStrategies as $strategy) {
            $this->registerStrategy($strategy);
        }
    }

    public function registerStrategy(MarkupLanguageParser $strategy): void
    {
        $this->parserStrategies[] = $strategy;
    }

    public function prepare(
        Metas $metas,
        ?FilesystemInterface $origin,
        string $sourcePath,
        string $destinationPath,
        string $fileName,
        int $initialHeaderLevel = 1
    ): void {
        if ($origin === null) {
            $origin = new Filesystem(new Local(getcwd()));
        }

        $this->metas = $metas;
        $this->environment = $this->createEnvironment(
            $sourcePath,
            $fileName,
            $origin,
            $destinationPath,
            $initialHeaderLevel
        );
    }

    /**
     * @todo Investigate if we can somehow dump the output format bit; the AST should not depend on the
     *       expected rendering.
     */
    public function parse(
        string $text,
        string $inputFormat = 'rst',
        string $outputFormat = 'html'
    ): DocumentNode {
        if ($this->metas === null || $this->environment === null) {
            // if Metas or Environment is not set; then the prepare method hasn't been called and we consider
            // this a one-off parse of dynamic RST content.
            $this->prepare(new Metas(), null, '', '', 'index');
        }

        $this->environment->setCurrentAbsolutePath(
            $this->buildPathOnFileSystem(
                $this->environment->getCurrentFileName(),
                $this->environment->getCurrentDirectory(),
                $inputFormat
            )
        );

        $format = $this->outputFormats->get($outputFormat);

        // TODO: The NodeRendererFactory on the Environment class is not used as much; refactor that away to remove this
        // runtime state setting
        $this->environment->setNodeRendererFactory($format->getNodeRendererFactory());

        $parser = $this->determineParser($inputFormat, $format);

        $this->environment->reset();

        $document = $parser->parse($this->environment, $text);

        if ($document instanceof DocumentNode) {
            $document->setVariables($this->environment->getVariables());
            $this->addDocumentToMetas($this->environment->getDestinationPath(), $outputFormat, $document);
        }

        $this->metas = null;
        $this->environment = null;

        return $document;
    }

    private function determineParser(string $fileExtension, OutputFormat $format): MarkupLanguageParser
    {
        foreach ($this->parserStrategies as $parserStrategy) {
            if ($parserStrategy->supports($fileExtension, $format)) {
                return $parserStrategy;
            }
        }

        throw new RuntimeException('Unable to parse document, no matching parsing strategy could be found');
    }

    private function createEnvironment(
        string $sourcePath,
        string $file,
        FilesystemInterface $origin,
        string $destinationPath,
        int $initialHeaderLevel
    ): Environment {
        $environment = new Environment(
            $destinationPath,
            $initialHeaderLevel,
            null,
            $this->logger,
            $origin,
            $this->metas,
            $this->urlGenerator
        );
        $environment->setCurrentFileName($file);
        $environment->setCurrentDirectory($sourcePath);

        return $environment;
    }

    private function buildPathOnFileSystem(string $file, string $currentDirectory, string $extension): string
    {
        return ltrim(sprintf('%s/%s.%s', trim($currentDirectory, '/'), $file, $extension), '/');
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

    private function buildDocumentUrl(Environment $environment, string $extension): string
    {
        return $environment->getUrl() . '.' . $extension;
    }

    private function buildOutputUrl(string $destinationPath, string $outputFormat, Environment $environment): string
    {
        $outputFolder = $destinationPath ? $destinationPath . '/' : '';

        return $outputFolder . $this->buildDocumentUrl($environment, $outputFormat);
    }

    private function addDocumentToMetas(string $destinationPath, string $outputFormat, DocumentNode $document): void
    {
        $this->metas->set(
            $this->environment->getCurrentFileName(),
            $this->buildOutputUrl($destinationPath, $outputFormat, $this->environment),
            $document->getTitle() ? $document->getTitle()->getValueString() : '',
            $document->getTitles(),
            $this->compileTableOfContents($document, $this->environment),
            (int) filemtime($this->environment->getCurrentAbsolutePath()),
            $document->getDependencies(),
            $this->environment->getLinks()
        );
    }
}
