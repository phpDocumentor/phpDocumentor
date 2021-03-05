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

class DocumentNodeRenderer implements NodeRenderer
{
    public function render(Node $node) : string
    {
        $document = '';

        foreach ($node->getNodes() as $childNode) {
            $document .= $childNode->render() . "\n";
        }

        return $document;
    }
}
