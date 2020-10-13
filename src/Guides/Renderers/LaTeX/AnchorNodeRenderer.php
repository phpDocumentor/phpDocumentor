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

namespace phpDocumentor\Guides\Renderers\LaTeX;

use phpDocumentor\Guides\Nodes\AnchorNode;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class AnchorNodeRenderer implements NodeRenderer
{
    /** @var AnchorNode */
    private $anchorNode;

    public function __construct(AnchorNode $anchorNode)
    {
        $this->anchorNode = $anchorNode;
    }

    public function render() : string
    {
        return $this->anchorNode->getEnvironment()->getRenderer()->render(
            'anchor.tex.twig',
            [
                'anchorNode' => $this->anchorNode,
            ]
        );
    }
}
