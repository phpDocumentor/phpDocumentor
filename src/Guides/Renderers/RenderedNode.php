<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\Renderers;

use phpDocumentor\Guides\Nodes\Node;

class RenderedNode
{
    /** @var Node */
    private $node;

    /** @var string */
    private $rendered;

    public function __construct(Node $node, string $rendered)
    {
        $this->node = $node;
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
