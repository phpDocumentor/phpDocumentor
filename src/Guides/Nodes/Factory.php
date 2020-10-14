<?php

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes;

use phpDocumentor\Guides\Environment;

interface Factory
{
    public function createQuoteNode(DocumentNode $documentNode) : QuoteNode;

    public function createTitleNode(Node $value, int $level, string $token) : TitleNode;

    public function createSeparatorNode(int $level) : SeparatorNode;

    /**
     * @param string[] $options
     */
    public function createImageNode(string $url, array $options = []) : ImageNode;

    public function createMetaNode(string $key, string $value) : MetaNode;

    public function createListNode() : ListNode;

    public function createMainNode() : MainNode;

    /**
     * @param string[] $lines
     */
    public function createCodeNode(array $lines) : CodeNode;

    /**
     * @param string[] $lines
     */
    public function createBlockNode(array $lines) : BlockNode;

    public function createSectionBeginNode(TitleNode $titleNode) : SectionBeginNode;

    public function createCallableNode(callable $callable) : CallableNode;

    /**
     * @param mixed[] $data
     */
    public function createDummyNode(array $data) : DummyNode;

    public function createParagraphNode(SpanNode $span) : ParagraphNode;

    public function createSectionEndNode(TitleNode $titleNode) : SectionEndNode;

    public function createFigureNode(ImageNode $image, ?Node $document = null) : FigureNode;

    /**
     * @param string[] $files
     * @param string[] $options
     */
    public function createTocNode(Environment $environment, array $files, array $options) : TocNode;

    public function createAnchorNode(?string $value = null) : AnchorNode;

    /**
     * Creates a node that returns the raw output from the given callable.
     *
     * Because the parsing process and the rendering process is separate, and the twig environment may not yet exist
     * during parsing, the given argument is a callable that will be called upon rendering.
     *
     * @param callable $value function that will return the rendered representation of this node
     */
    public function createRawNode(callable $value) : RawNode;

    public function createDocumentNode(Environment $environment) : DocumentNode;

    /**
     * @param string|callable $before
     * @param string|callable $after
     */
    public function createWrapperNode(?Node $node, $before = '', $after = '') : WrapperNode;
}
