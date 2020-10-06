<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Nodes\FigureNode;
use phpDocumentor\Guides\Renderers\NodeRenderer;
use phpDocumentor\Guides\TemplateRenderer;

class FigureNodeRenderer implements NodeRenderer
{
    /** @var FigureNode */
    private $figureNode;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(FigureNode $figureNode, TemplateRenderer $templateRenderer)
    {
        $this->figureNode       = $figureNode;
        $this->templateRenderer = $templateRenderer;
    }

    public function render() : string
    {
        return $this->templateRenderer->render('figure.html.twig', [
            'figureNode' => $this->figureNode,
        ]);
    }
}
