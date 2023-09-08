<?php

namespace phpDocumentor\Guides\Nodes;

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Reflection\Fqsen;

final class DocblockNode extends AbstractNode
{
    private Descriptor|null $descriptor;

    public function __construct(private Fqsen $fqsen)
    {

    }

    public function getFqsen(): Fqsen
    {
        return $this->fqsen;
    }

    public function withDescriptor(Descriptor|null $descriptor): self
    {
        $that = clone $this;
        $that->descriptor = $descriptor;

        return $that;
    }

    public function getDescriptor(): Descriptor|null
    {
        return $this->descriptor;
    }
}
