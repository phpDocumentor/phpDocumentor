<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes\PHP;

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Guides\Nodes\InlineCompoundNode;
use phpDocumentor\Guides\Nodes\InlineToken\PHPReferenceNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\TitleNode;

/** @extends DescriptorNode<ElementInterface> */
final class ElementName extends DescriptorNode
{
    /** @return TitleNode|PHPReferenceNode|DescriptorNode<ElementInterface> */
    public function withDescriptor(Descriptor $descriptor): Node
    {
        $that = parent::withDescriptor($descriptor);

        if ($that->descriptor === null) {
            return $that;
        }

        $refrence = new PHPReferenceNode(
            'class',
            $that->descriptor->getFullyQualifiedStructuralElementName(),
        );

        $refrence->setDescriptor($descriptor);

        if ((bool) ($that->getOption('title', false))) {
            return new TitleNode(
                new InlineCompoundNode([$refrence]),
                (int) $that->getOption('level', 2),
                '',
            );
        }

        return $refrence;
    }
}
