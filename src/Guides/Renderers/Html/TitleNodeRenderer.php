<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Nodes\TitleNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class TitleNodeRenderer implements NodeRenderer
{
    /** @var TitleNode */
    private $titleNode;

    /** @var Renderer */
    private $renderer;

    public function __construct(TitleNode $titleNode)
    {
        $this->titleNode        = $titleNode;
        $this->renderer = $titleNode->getEnvironment()->getRenderer();
    }

    public function render() : string
    {
        return $this->renderer->render('header-title.html.twig', [
            'titleNode' => $this->titleNode,
        ]);
    }
}
