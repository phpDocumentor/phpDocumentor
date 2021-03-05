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

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SectionEndNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class SectionEndNodeRenderer implements NodeRenderer
{
    /** @var Renderer */
    private $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function render(Node $node) : string
    {
        if ($node instanceof SectionEndNode === false) {
            throw new \InvalidArgumentException('Invalid node presented');
        }


        return $this->renderer->render(
            'section-end.html.twig',
            [
                'sectionEndNode' => $node,
            ]
        );
    }
}
