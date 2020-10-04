<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use Doctrine\Common\EventManager;
use IteratorAggregate;
use phpDocumentor\Guides\BuildContext;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Event\PostBuildRenderEvent;
use phpDocumentor\Guides\RestructuredText\Event\PreNodeRenderEvent;
use phpDocumentor\Guides\RestructuredText\Listener\AssetsCopyListener;
use phpDocumentor\Guides\RestructuredText\Listener\CopyImagesListener;
use phpDocumentor\Guides\RestructuredText\Nodes\DocumentNode;
use phpDocumentor\Guides\RestructuredText\Nodes\NodeTypes;
use phpDocumentor\Guides\RestructuredText\References\Doc;
use phpDocumentor\Guides\RestructuredText\References\Reference;
use Psr\Log\LoggerInterface;
use function array_merge;

class Kernel
{
    /** @var Configuration */
    private $configuration;

    /** @var Directive[] */
    private $directives;

    /** @var Reference[] */
    private $references;

    private $buildContext;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        Configuration $configuration,
        IteratorAggregate $directives,
        IteratorAggregate $references,
        BuildContext $buildContext,
        LoggerInterface $logger
    ) {
        $this->configuration = $configuration;

        $this->directives = array_merge([
            new Directives\Dummy(),
            new Directives\CodeBlock(),
            new Directives\Raw(),
            new Directives\Replace(),
            new Directives\Toctree(),
        ], $this->configuration->getFormat()->getDirectives(), iterator_to_array($directives));

        $this->references = array_merge([
            new References\Doc(),
            new References\Doc('ref', true),
        ], $this->createReferences(), iterator_to_array($references));

        $this->buildContext = $buildContext;
        $this->logger = $logger;
    }

    public function initBuilder(Builder $builder) : void
    {
        $this->initializeListeners($this->getConfiguration()->getEventManager());
    }

    private function initializeListeners(EventManager $eventManager) : void
    {
        $eventManager->addEventListener(
            PreNodeRenderEvent::PRE_NODE_RENDER,
            new CopyImagesListener($this->buildContext, $this->logger)
        );

        $eventManager->addEventListener(
            [PostBuildRenderEvent::POST_BUILD_RENDER],
            new AssetsCopyListener($this->buildContext->getOutputFilesystem())
        );
    }

    public function getConfiguration() : Configuration
    {
        return $this->configuration;
    }

    /**
     * @return Directive[]
     */
    public function getDirectives() : array
    {
        return $this->directives;
    }

    /**
     * @return Reference[]
     */
    public function getReferences() : array
    {
        return $this->references;
    }

    public function getNodes() : array
    {
        return [
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
    }

    public function postParse(DocumentNode $document) : void
    {
    }

    /**
     * @return Doc[]
     */
    protected function createReferences() : array
    {
        return [];
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
