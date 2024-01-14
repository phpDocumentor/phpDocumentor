<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes\PHP;

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Guides\Nodes\CompoundNode;
use phpDocumentor\Guides\Nodes\Node;

/**
 * @template TDescriptor of Descriptor
 * @extends CompoundNode<Node>
 */
abstract class DescriptorNode extends CompoundNode
{
    /** @var TDescriptor|null */
    protected Descriptor|null $descriptor = null;

    public function __construct(private string $query)
    {
        parent::__construct();
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @param TDescriptor $descriptor
     *
     * @return self<TDescriptor>
     */
    public function withDescriptor(Descriptor $descriptor): Node
    {
        $clone = clone $this;
        $clone->descriptor = $descriptor;

        return $clone;
    }
}
