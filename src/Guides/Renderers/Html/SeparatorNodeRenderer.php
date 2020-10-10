<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Nodes\SeparatorNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class SeparatorNodeRenderer implements NodeRenderer
{
    /** @var Renderer */
    private $renderer;

    public function __construct(SeparatorNode $separatorNode)
    {
        $this->renderer = $separatorNode->getEnvironment()->getRenderer();
    }

    public function render() : string
    {
        return $this->renderer->render('separator.html.twig');
    }
}
