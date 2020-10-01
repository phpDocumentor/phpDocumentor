<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\NodeFactory;

use Doctrine\Common\EventManager;
use phpDocumentor\Guides\RestructuredText\Environment;
use phpDocumentor\Guides\RestructuredText\Event\PostNodeCreateEvent;
use phpDocumentor\Guides\RestructuredText\Nodes\AnchorNode;
use phpDocumentor\Guides\RestructuredText\Nodes\BlockNode;
use phpDocumentor\Guides\RestructuredText\Nodes\CallableNode;
use phpDocumentor\Guides\RestructuredText\Nodes\CodeNode;
use phpDocumentor\Guides\RestructuredText\Nodes\DefinitionListNode;
use phpDocumentor\Guides\RestructuredText\Nodes\DocumentNode;
use phpDocumentor\Guides\RestructuredText\Nodes\DummyNode;
use phpDocumentor\Guides\RestructuredText\Nodes\FigureNode;
use phpDocumentor\Guides\RestructuredText\Nodes\ImageNode;
use phpDocumentor\Guides\RestructuredText\Nodes\ListNode;
use phpDocumentor\Guides\RestructuredText\Nodes\MainNode;
use phpDocumentor\Guides\RestructuredText\Nodes\MetaNode;
use phpDocumentor\Guides\RestructuredText\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Nodes\NodeTypes;
use phpDocumentor\Guides\RestructuredText\Nodes\ParagraphNode;
use phpDocumentor\Guides\RestructuredText\Nodes\QuoteNode;
use phpDocumentor\Guides\RestructuredText\Nodes\RawNode;
use phpDocumentor\Guides\RestructuredText\Nodes\SectionBeginNode;
use phpDocumentor\Guides\RestructuredText\Nodes\SectionEndNode;
use phpDocumentor\Guides\RestructuredText\Nodes\SeparatorNode;
use phpDocumentor\Guides\RestructuredText\Nodes\SpanNode;
use phpDocumentor\Guides\RestructuredText\Nodes\TableNode;
use phpDocumentor\Guides\RestructuredText\Nodes\TitleNode;
use phpDocumentor\Guides\RestructuredText\Nodes\TocNode;
use phpDocumentor\Guides\RestructuredText\Nodes\WrapperNode;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\DefinitionList;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use InvalidArgumentException;
use function sprintf;

class DefaultNodeFactory implements NodeFactory
{
    /** @var EventManager */
    private $eventManager;

    /** @var NodeInstantiator[] */
    private $nodeInstantiators = [];

    public function __construct(EventManager $eventManager, NodeInstantiator ...$nodeInstantiators)
    {
        $this->eventManager = $eventManager;

        foreach ($nodeInstantiators as $nodeInstantiator) {
            $this->nodeInstantiators[$nodeInstantiator->getType()] = $nodeInstantiator;
        }
    }

    public function createDocumentNode(Environment $environment) : DocumentNode
    {
        /** @var DocumentNode $document */
        $document = $this->create(NodeTypes::DOCUMENT, [$environment]);

        return $document;
    }

    /**
     * @param string[] $files
     * @param string[] $options
     */
    public function createTocNode(Environment $environment, array $files, array $options) : TocNode
    {
        /** @var TocNode $tocNode */
        $tocNode = $this->create(NodeTypes::TOC, [$environment, $files, $options]);

        return $tocNode;
    }

    public function createTitleNode(Node $value, int $level, string $token) : TitleNode
    {
        /** @var TitleNode $titleNode */
        $titleNode = $this->create(NodeTypes::TITLE, [$value, $level, $token]);

        return $titleNode;
    }

    public function createSeparatorNode(int $level) : SeparatorNode
    {
        /** @var SeparatorNode $separatorNode */
        $separatorNode = $this->create(NodeTypes::SEPARATOR, [$level]);

        return $separatorNode;
    }

    /**
     * @param string[] $lines
     */
    public function createBlockNode(array $lines) : BlockNode
    {
        /** @var BlockNode $blockNode */
        $blockNode = $this->create(NodeTypes::BLOCK, [$lines]);

        return $blockNode;
    }

    /**
     * @param string[] $lines
     */
    public function createCodeNode(array $lines) : CodeNode
    {
        /** @var CodeNode $codeNode */
        $codeNode = $this->create(NodeTypes::CODE, [$lines]);

        return $codeNode;
    }

    public function createQuoteNode(DocumentNode $documentNode) : QuoteNode
    {
        /** @var QuoteNode $quoteNode */
        $quoteNode = $this->create(NodeTypes::QUOTE, [$documentNode]);

        return $quoteNode;
    }

    public function createParagraphNode(SpanNode $span) : ParagraphNode
    {
        /** @var ParagraphNode $paragraphNode */
        $paragraphNode = $this->create(NodeTypes::PARAGRAPH, [$span]);

        return $paragraphNode;
    }

    public function createAnchorNode(?string $value = null) : AnchorNode
    {
        /** @var AnchorNode $anchorNode */
        $anchorNode = $this->create(NodeTypes::ANCHOR, [$value]);

        return $anchorNode;
    }

    public function createListNode() : ListNode
    {
        /** @var ListNode $listNode */
        $listNode = $this->create(NodeTypes::LIST, []);

        return $listNode;
    }

    public function createTableNode(Parser\TableSeparatorLineConfig $separatorLineConfig, string $type, LineChecker $lineChecker) : TableNode
    {
        /** @var TableNode $tableNode */
        $tableNode = $this->create(NodeTypes::TABLE, [$separatorLineConfig, $type, $lineChecker]);

        return $tableNode;
    }

    /**
     * @param string|string[]|SpanNode $span
     */
    public function createSpanNode(Parser $parser, $span) : SpanNode
    {
        /** @var SpanNode $span */
        $span = $this->create(NodeTypes::SPAN, [$parser, $span]);

        return $span;
    }

    public function createDefinitionListNode(DefinitionList $definitionList) : DefinitionListNode
    {
        /** @var DefinitionListNode $definitionListNode */
        $definitionListNode = $this->create(NodeTypes::DEFINITION_LIST, [$definitionList]);

        return $definitionListNode;
    }

    public function createWrapperNode(?Node $node, string $before = '', string $after = '') : WrapperNode
    {
        /** @var WrapperNode $wrapperNode */
        $wrapperNode = $this->create(NodeTypes::WRAPPER, [$node, $before, $after]);

        return $wrapperNode;
    }

    public function createFigureNode(ImageNode $image, ?Node $document = null) : FigureNode
    {
        /** @var FigureNode $figureNode */
        $figureNode = $this->create(NodeTypes::FIGURE, [$image, $document]);

        return $figureNode;
    }

    /**
     * @param string[] $options
     */
    public function createImageNode(string $url, array $options = []) : ImageNode
    {
        /** @var ImageNode $imageNode */
        $imageNode = $this->create(NodeTypes::IMAGE, [$url, $options]);

        return $imageNode;
    }

    public function createMetaNode(string $key, string $value) : MetaNode
    {
        /** @var MetaNode $metaNode */
        $metaNode = $this->create(NodeTypes::META, [$key, $value]);

        return $metaNode;
    }

    public function createRawNode(string $value) : RawNode
    {
        /** @var RawNode $rawNode */
        $rawNode = $this->create(NodeTypes::RAW, [$value]);

        return $rawNode;
    }

    /**
     * @param mixed[] $data
     */
    public function createDummyNode(array $data) : DummyNode
    {
        /** @var DummyNode $dummyNode */
        $dummyNode = $this->create(NodeTypes::DUMMY, [$data]);

        return $dummyNode;
    }

    public function createMainNode() : MainNode
    {
        /** @var MainNode $mainNode */
        $mainNode = $this->create(NodeTypes::MAIN, []);

        return $mainNode;
    }

    public function createCallableNode(callable $callable) : CallableNode
    {
        /** @var CallableNode $callableNode */
        $callableNode = $this->create(NodeTypes::CALLABLE, [$callable]);

        return $callableNode;
    }

    public function createSectionBeginNode(TitleNode $titleNode) : SectionBeginNode
    {
        /** @var SectionBeginNode $sectionBeginNode */
        $sectionBeginNode = $this->create(NodeTypes::SECTION_BEGIN, [$titleNode]);

        return $sectionBeginNode;
    }

    public function createSectionEndNode(TitleNode $titleNode) : SectionEndNode
    {
        /** @var SectionEndNode $sectionEndNode */
        $sectionEndNode = $this->create(NodeTypes::SECTION_END, [$titleNode]);

        return $sectionEndNode;
    }

    /**
     * @param mixed[] $arguments
     */
    private function create(string $type, array $arguments) : Node
    {
        $node = $this->getNodeInstantiator($type)->create($arguments);

        $this->eventManager->dispatchEvent(
            PostNodeCreateEvent::POST_NODE_CREATE,
            new PostNodeCreateEvent($node)
        );

        return $node;
    }

    private function getNodeInstantiator(string $type) : NodeInstantiator
    {
        if (! isset($this->nodeInstantiators[$type])) {
            throw new InvalidArgumentException(sprintf('Could not find node instantiator of type %s', $type));
        }

        return $this->nodeInstantiators[$type];
    }
}
