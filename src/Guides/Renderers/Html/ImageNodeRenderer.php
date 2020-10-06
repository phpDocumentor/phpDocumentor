<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Nodes\ImageNode;
use phpDocumentor\Guides\Renderers\NodeRenderer;
use phpDocumentor\Guides\TemplateRenderer;

class ImageNodeRenderer implements NodeRenderer
{
    /** @var ImageNode */
    private $imageNode;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(ImageNode $imageNode, TemplateRenderer $templateRenderer)
    {
        $this->imageNode        = $imageNode;
        $this->templateRenderer = $templateRenderer;
    }

    public function render() : string
    {
        return $this->templateRenderer->render('image.html.twig', [
            'imageNode' => $this->imageNode,
        ]);
    }
}
