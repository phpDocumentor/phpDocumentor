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

use phpDocumentor\Guides\Nodes\ImageNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class ImageNodeRenderer implements NodeRenderer
{
    /** @var ImageNode */
    private $imageNode;

    /** @var Renderer */
    private $renderer;

    public function __construct(ImageNode $imageNode)
    {
        $this->imageNode = $imageNode;
        $this->renderer = $imageNode->getEnvironment()->getRenderer();
    }

    public function render() : string
    {
        return $this->renderer->render(
            'image.html.twig',
            [
                'imageNode' => $this->imageNode,
            ]
        );
    }
}
