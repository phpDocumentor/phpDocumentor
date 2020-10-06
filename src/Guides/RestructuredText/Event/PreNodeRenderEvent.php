<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Event;

use Doctrine\Common\EventArgs;
use phpDocumentor\Guides\Nodes\Node;

final class PreNodeRenderEvent extends EventArgs
{
    public const PRE_NODE_RENDER = 'preNodeRender';

    /** @var Node */
    private $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function getNode() : Node
    {
        return $this->node;
    }
}
