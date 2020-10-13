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

use phpDocumentor\Guides\Nodes\ParagraphNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class ParagraphNodeRenderer implements NodeRenderer
{
    /** @var ParagraphNode */
    private $paragraphNode;

    /** @var Renderer */
    private $renderer;

    public function __construct(ParagraphNode $paragraphNode)
    {
        $this->paragraphNode = $paragraphNode;
        $this->renderer = $paragraphNode->getEnvironment()->getRenderer();
    }

    public function render() : string
    {
        return $this->renderer->render(
            'paragraph.html.twig',
            [
                'paragraphNode' => $this->paragraphNode,
            ]
        );
    }
}
