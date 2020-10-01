<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\NodeFactory;

use phpDocumentor\Guides\RestructuredText\Environment;
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
use phpDocumentor\Guides\RestructuredText\Parser\TableSeparatorLineConfig;

interface NodeFactory
{
    public function createDocumentNode(Environment $environment) : DocumentNode;

    /**
     * @param string[] $files
     * @param string[] $options
     */
    public function createTocNode(Environment $environment, array $files, array $options) : TocNode;

    public function createTitleNode(Node $value, int $level, string $token) : TitleNode;

    public function createSeparatorNode(int $level) : SeparatorNode;

    /**
     * @param string[] $lines
     */
    public function createBlockNode(array $lines) : BlockNode;

    /**
     * @param string[] $lines
     */
    public function createCodeNode(array $lines) : CodeNode;

    public function createQuoteNode(DocumentNode $documentNode) : QuoteNode;

    public function createParagraphNode(SpanNode $span) : ParagraphNode;

    public function createAnchorNode(?string $value = null) : AnchorNode;

    public function createListNode() : ListNode;

    public function createTableNode(TableSeparatorLineConfig $separatorLineConfig, string $type, LineChecker $lineChecker) : TableNode;

    /**
     * @param string|string[]|SpanNode $span
     */
    public function createSpanNode(Parser $parser, $span) : SpanNode;

    public function createDefinitionListNode(DefinitionList $definitionList) : DefinitionListNode;

    public function createWrapperNode(?Node $node, string $before = '', string $after = '') : WrapperNode;

    public function createFigureNode(ImageNode $image, ?Node $document = null) : FigureNode;

    /**
     * @param string[] $options
     */
    public function createImageNode(string $url, array $options = []) : ImageNode;

    public function createMetaNode(string $key, string $value) : MetaNode;

    public function createRawNode(string $value) : RawNode;

    /**
     * @param mixed[] $data
     */
    public function createDummyNode(array $data) : DummyNode;

    public function createMainNode() : MainNode;

    public function createCallableNode(callable $callable) : CallableNode;

    public function createSectionBeginNode(TitleNode $titleNode) : SectionBeginNode;

    public function createSectionEndNode(TitleNode $titleNode) : SectionEndNode;
}
