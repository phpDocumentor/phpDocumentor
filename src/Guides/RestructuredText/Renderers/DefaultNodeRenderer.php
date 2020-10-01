<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Renderers;

use phpDocumentor\Guides\RestructuredText\Nodes\Node;

class DefaultNodeRenderer implements NodeRenderer
{
    /** @var Node */
    private $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function render() : string
    {
        $value = $this->node->getValue();

        if ($value instanceof Node) {
            return $value->render();
        }

        if ($value === null) {
            return '';
        }

        return $value;
    }
}
