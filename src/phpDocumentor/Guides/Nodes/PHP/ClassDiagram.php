<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes\PHP;

use phpDocumentor\Descriptor\Descriptor;

/** @extends DescriptorNode<Descriptor> */
final class ClassDiagram extends DescriptorNode
{
    private string $caption = '';

    public function __construct(string $query)
    {
        parent::__construct("$.documentationSets.*[?(type(@) == 'ApiSetDescriptor')].indexes.classes.*" . $query);
    }

    public function setCaption(string|null $caption): self
    {
        $that = clone $this;
        $that->caption = $caption ?? '';

        return $that;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }
}
