<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Nodes\AnchorNode;
use phpDocumentor\Guides\Renderers\NodeRenderer;
use phpDocumentor\Guides\TemplateRenderer;

class AnchorNodeRenderer implements NodeRenderer
{
    /** @var AnchorNode */
    private $anchorNode;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(AnchorNode $anchorNode, TemplateRenderer $templateRenderer)
    {
        $this->anchorNode       = $anchorNode;
        $this->templateRenderer = $templateRenderer;
    }

    public function render() : string
    {
        return $this->templateRenderer->render('anchor.html.twig', [
            'anchorNode' => $this->anchorNode,
        ]);
    }
}
