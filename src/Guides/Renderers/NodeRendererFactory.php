<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers;

use phpDocumentor\Guides\Nodes\Node;

interface NodeRendererFactory
{
    public function create(Node $node) : NodeRenderer;
}
