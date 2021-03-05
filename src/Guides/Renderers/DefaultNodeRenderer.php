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

class DefaultNodeRenderer implements NodeRenderer
{
    public function render(Node $node) : string
    {
        $value = $node->getValue();

        if ($value instanceof Node) {
            return $value->render();
        }

        if ($value === null) {
            return '';
        }

        return $value;
    }
}
