<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Nodes\FigureNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class FigureNodeRenderer implements NodeRenderer
{
    /** @var FigureNode */
    private $figureNode;

    /** @var Renderer */
    private $renderer;

    public function __construct(FigureNode $figureNode)
    {
        $this->figureNode       = $figureNode;
        $this->renderer = $figureNode->getEnvironment()->getRenderer();
    }

    public function render() : string
    {
        return $this->renderer->render('figure.html.twig', [
            'figureNode' => $this->figureNode,
        ]);
    }
}
