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

use phpDocumentor\Guides\Nodes\CallableNode;

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
