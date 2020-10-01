<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Renderers;

use phpDocumentor\Guides\RestructuredText\Nodes\Node;

class RenderedNode
{
    /** @var Node */
    private $node;

    /** @var string */
    private $rendered;

    public function __construct(Node $node, string $rendered)
    {
        $this->node     = $node;
        $this->rendered = $rendered;
    }

    public function getNode() : Node
    {
        return $this->node;
    }

    public function setRendered(string $rendered) : void
    {
        $this->rendered = $rendered;
    }

    public function getRendered() : string
    {
        return $this->rendered;
    }
}
