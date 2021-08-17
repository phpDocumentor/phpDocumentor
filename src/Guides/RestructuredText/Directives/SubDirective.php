<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\BlockNode;
use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser;

/**
 * A directive that parses the sub block and call the processSub that can
 * be overloaded, like :
 *
 * .. sub-directive::
 *      Some block of code
 *
 *      You can imagine anything here, like adding *emphasis*, lists or
 *      titles
 */
abstract class SubDirective extends Directive
{
    /**
     * @param string[] $options
     */
    final public function process(
        Parser $parser,
        ?Node $node,
        string $variable,
        string $data,
        array $options
    ): void {
        /*
         * A BlockNode indicates that the content was passed in "raw" and should
         * be sub-parsed. This is the main use-case.
         *
         * However, there are some odd cases where a directive appears
         * to "end"... and DocumentParser continues parsing the next
         * section, for example as a ParagraphNode or ListNode. When
         * that terminates, the original directive is THEN processed
         * and called.
         *
         * A key example is the "class::" directive (see class-directive.rst test case).
         * That is where it is legal to have a format like this:
         *
         *      .. class:: special-list
         *
         *      - Test list item 1.
         *      - Test list item 2.
         *
         * Notice the 2 list items are NOT indented. This is legal, and ultimately
         * those two items would be parsed as a ListNode and THEN passed to
         * ClassDirective (which extends SubDirective) for processing.
         */
        if ($node instanceof BlockNode) {
            $document = $parser->getSubParser()->parseLocal($node->getValue());
        } elseif ($node instanceof CodeNode) {
            $document = $parser->getSubParser()->parseLocal($node->getValue());
        } else {
            // If the $node is null, it represents a node with no content.
            // Some directives - like "figure" - both allow content AND no content.
            $document = $node;
        }

        $newNode = $this->processSub($parser, $document, $variable, $data, $options);

        if ($newNode === null) {
            return;
        }

        if ($variable !== '') {
            $parser->getEnvironment()->setVariable($variable, $newNode);
        } else {
            $parser->getDocument()->addNode($newNode);
        }
    }

    /**
     * @param string[] $options
     */
    public function processSub(
        Parser $parser,
        ?Node $document,
        string $variable,
        string $data,
        array $options
    ): ?Node {
        return null;
    }

    public function wantCode(): bool
    {
        return true;
    }
}
