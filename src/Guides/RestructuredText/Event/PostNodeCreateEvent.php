<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Event;

use Doctrine\Common\EventArgs;
use phpDocumentor\Guides\Nodes\Node;

final class PostNodeCreateEvent extends EventArgs
{
    public const POST_NODE_CREATE = 'postNodeCreate';

    /** @var Node */
    private $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function getNode(): Node
    {
        return $this->node;
    }
}
