<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Renderers;

use phpDocumentor\Guides\RestructuredText\Nodes\CallableNode;

class CallableNodeRenderer implements NodeRenderer
{
    /** @var CallableNode */
    private $callableNode;

    public function __construct(CallableNode $callableNode)
    {
        $this->callableNode = $callableNode;
    }

    public function render() : string
    {
        return $this->callableNode->getCallable()();
    }
}
