<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes\PHP;

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;

/** @extends DescriptorNode<ClassInterface> */
final class ClassList extends DescriptorNode
{
    /** @param DescriptorNode<Descriptor>[] $blueprint */
    public function __construct(private readonly array $blueprint, string $query)
    {
        parent::__construct("$.documentationSets.*[?(type(@) == 'ApiSetDescriptor')].indexes.classes.*" . $query);
    }

    /** @return DescriptorNode<Descriptor>[] */
    public function getBlueprint(): array
    {
        return $this->blueprint;
    }
}
