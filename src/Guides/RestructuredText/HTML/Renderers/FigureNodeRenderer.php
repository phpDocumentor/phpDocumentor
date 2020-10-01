<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML\Renderers;

use phpDocumentor\Guides\RestructuredText\Nodes\FigureNode;
use phpDocumentor\Guides\RestructuredText\Renderers\NodeRenderer;
use phpDocumentor\Guides\RestructuredText\Templates\TemplateRenderer;

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
