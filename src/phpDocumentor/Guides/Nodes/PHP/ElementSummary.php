<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes\PHP;

use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Guides\Nodes\Inline\PlainTextInlineNode;
use phpDocumentor\Guides\Nodes\InlineCompoundNode;
use phpDocumentor\Guides\Nodes\ParagraphNode;

/** @extends DescriptorNode<ElementInterface> */
final class ElementSummary extends DescriptorNode
{
    public function getChildren(): array
    {
        if ($this->descriptor === null) {
            return [];
        }

        return [
            new ParagraphNode(
                [new InlineCompoundNode([new PlainTextInlineNode($this->descriptor->getSummary())])],
            ),
        ];
    }

    public function getValue(): array
    {
        return $this->getChildren();
    }
}
