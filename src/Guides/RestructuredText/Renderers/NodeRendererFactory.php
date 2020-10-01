<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Renderers;

use phpDocumentor\Guides\RestructuredText\Nodes\Node;

interface NodeRendererFactory
{
    public function create(Node $node) : NodeRenderer;
}
