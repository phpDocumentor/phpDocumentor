<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes\PHP;

use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Guides\Nodes\InlineToken\PHPReferenceNode;

/** @extends DescriptorNode<ElementInterface> */
final class ElementName extends DescriptorNode
{
    public function getChildren(): array
    {
        if ($this->descriptor === null) {
            return [];
        }

        $refrence = new PHPReferenceNode(
            'class',
            $this->descriptor->getFullyQualifiedStructuralElementName(),
        );

        $refrence->setDescriptor($this->descriptor);

        return [$refrence];
    }

    public function getValue(): array
    {
        return $this->getChildren();
    }
}
