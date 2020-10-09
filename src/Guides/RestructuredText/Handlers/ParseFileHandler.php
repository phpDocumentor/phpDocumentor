<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Handlers;

use Doctrine\Common\EventManager;
use phpDocumentor\Guides\Documents;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\Nodes;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\NodeTypes;
use phpDocumentor\Guides\RestructuredText\NodeFactory\DefaultNodeFactory;
use phpDocumentor\Guides\RestructuredText\ParseFileCommand;
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

    /** @var \IteratorAggregate */
    private $directives;
    /**
     * @var \IteratorAggregate
     */
    private $references;
    /**
     * @var EventManager
     */
    private $eventManager;

    public function __construct(
        Metas $metas,
        Documents $documents,
        LoggerInterface $logger,
        EventManager $eventManager,
        \IteratorAggregate $directives,
        \IteratorAggregate $references
    ) {
        $this->metas = $metas;
        $this->documents = $documents;
        $this->logger = $logger;
        $this->directives = $directives;
        $this->references = $references;
        $this->eventManager = $eventManager;
    }

    public function handle(ParseFileCommand $command): void
    {
        $configuration = $command->getConfiguration();
        $file = $command->getFile();

        $environment = new Environment($configuration, $this->logger, $command->getOrigin());
        $environment->setMetas($this->metas);
        $environment->setCurrentFileName($file);
        $environment->setCurrentDirectory($command->getDirectory());

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
            NodeTypes::SECTION_END => Nodes\SectionEndNode::class
        ];

        $templateRenderer = $configuration->getTemplateRenderer();

        $nodeFactory = DefaultNodeFactory::createFromRegistry(
            $this->eventManager,
            $configuration->getFormat(),
            $templateRenderer,
            $nodeRegistry
        );

        $parser = new Parser(
            $configuration,
            $templateRenderer,
            $environment,
            $this->eventManager,
            $nodeFactory,
            iterator_to_array($this->directives),
            iterator_to_array($this->references)
        );

        $fileAbsolutePath = $this->buildPathOnFileSystem(
            $file,
            $command->getDirectory(),
            $configuration->getSourceFileExtension()
        );

        $this->logger->info(sprintf('Parsing %s', $fileAbsolutePath));
        $document = $parser->parseFile($fileAbsolutePath);

        $this->documents->addDocument($file, $document);

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
