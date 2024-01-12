<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes\PHP;

use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;

/** @extends DescriptorNode<ElementInterface> */
final class ElementDescription extends DescriptorNode
{
    public function getDescription(): DescriptionDescriptor
    {
        return $this->descriptor?->getDescription() ?? DescriptionDescriptor::createEmpty();
    }
}
