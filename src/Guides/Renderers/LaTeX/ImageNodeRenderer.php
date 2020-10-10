<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers\LaTeX;

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
        $this->imageNode        = $imageNode;
        $this->renderer = $imageNode->getEnvironment()->getRenderer();
    }

    public function render() : string
    {
        return $this->renderer->render('image.tex.twig', [
            'imageNode' => $this->imageNode,
        ]);
    }
}
