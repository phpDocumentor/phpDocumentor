<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Handlers;

use Doctrine\Common\EventManager;
use InvalidArgumentException;
use IteratorAggregate;
use phpDocumentor\Guides\Configuration;
use phpDocumentor\Guides\Documents;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Markdown\Parser as MarkdownParser;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\Nodes;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\NodeTypes;
use phpDocumentor\Guides\References\Reference;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\NodeFactory\DefaultNodeFactory;
use phpDocumentor\Guides\RestructuredText\ParseFileCommand;
use phpDocumentor\Guides\RestructuredText\Parser;
use Psr\Log\LoggerInterface;
use function filemtime;
use function iterator_to_array;
use function ltrim;
use function sprintf;
use function trim;

final class ParseFileHandler
{
    /** @var Metas */
    private $metas;

    /** @var Documents */
    private $documents;

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
        Documents $documents,
        Renderer $renderer,
        LoggerInterface $logger,
        EventManager $eventManager,
        IteratorAggregate $directives,
        IteratorAggregate $references
    ) {
        $this->metas = $metas;
        $this->documents = $documents;
        $this->logger = $logger;
        $this->directives = $directives;
        $this->references = $references;
        $this->eventManager = $eventManager;
        $this->renderer = $renderer;
    }

    public function handle(ParseFileCommand $command) : void
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

        $this->documents->addDocument($file, $document);

        $outputFolder = $configuration->getOutputFolder() ? $configuration->getOutputFolder() . '/' : '';
        $url = $outputFolder . $this->buildDocumentUrl($document, $configuration->getFileExtension());

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

    private function parseRestructuredText(
        Configuration $configuration,
        Environment $environment,
        string $fileAbsolutePath
    ) : DocumentNode {
        $nodeFactory = $this->createNodeFactory($configuration, $environment);

        $parser = new Parser(
            $configuration,
            $environment,
            $this->eventManager,
            $nodeFactory,
            iterator_to_array($this->directives),
            iterator_to_array($this->references)
        );

        return $parser->parse($this->getFileContents($environment, $fileAbsolutePath));
    }

    private function parseMarkdown(
        Configuration $configuration,
        Environment $environment,
        string $fileAbsolutePath
    ) : DocumentNode {
        $nodeFactory = $this->createNodeFactory($configuration, $environment);
        $environment->setNodeFactory($nodeFactory);
        $parser = new MarkdownParser($environment);

        return $parser->parse($this->getFileContents($environment, $fileAbsolutePath));
    }

    private function getFileContents(Environment $environment, string $file) : string
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

    private function createNodeFactory(Configuration $configuration, Environment $environment) : DefaultNodeFactory
    {
        $nodeRegistry = [
            NodeTypes::DOCUMENT => Nodes\DocumentNode::class,
            NodeTypes::SPAN => Nodes\SpanNode::class,
            NodeTypes::TOC => Nodes\TocNode::class,
            NodeTypes::TITLE => Nodes\TitleNode::class,
            NodeTypes::SEPARATOR => Nodes\SeparatorNode::class,
            NodeTypes::CODE => Nodes\CodeNode::class,
            NodeTypes::QUOTE => Nodes\QuoteNode::class,
            NodeTypes::PARAGRAPH => Nodes\ParagraphNode::class,
            NodeTypes::ANCHOR => Nodes\AnchorNode::class,
            NodeTypes::LIST => Nodes\ListNode::class,
            NodeTypes::TABLE => Nodes\TableNode::class,
            NodeTypes::DEFINITION_LIST => Nodes\DefinitionListNode::class,
            NodeTypes::WRAPPER => Nodes\WrapperNode::class,
            NodeTypes::FIGURE => Nodes\FigureNode::class,
            NodeTypes::IMAGE => Nodes\ImageNode::class,
            NodeTypes::META => Nodes\MetaNode::class,
            NodeTypes::RAW => Nodes\RawNode::class,
            NodeTypes::DUMMY => Nodes\DummyNode::class,
            NodeTypes::MAIN => Nodes\MainNode::class,
            NodeTypes::BLOCK => Nodes\BlockNode::class,
            NodeTypes::CALLABLE => Nodes\CallableNode::class,
            NodeTypes::SECTION_BEGIN => Nodes\SectionBeginNode::class,
            NodeTypes::SECTION_END => Nodes\SectionEndNode::class,
        ];

        return DefaultNodeFactory::createFromRegistry(
            $this->eventManager,
            $configuration->getFormat(),
            $environment,
            $nodeRegistry
        );
    }
}
