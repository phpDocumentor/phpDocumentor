<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Nodes\AnchorNode;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class AnchorNodeRenderer implements NodeRenderer
{
    /** @var AnchorNode */
    private $anchorNode;

    public function __construct(AnchorNode $anchorNode)
    {
        $this->anchorNode = $anchorNode;
    }

    public function render() : string
    {
        return $this->anchorNode->getEnvironment()->getRenderer()
            ->render(
                'anchor.html.twig', [
                'anchorNode' => $this->anchorNode,
            ]);
    }
}
