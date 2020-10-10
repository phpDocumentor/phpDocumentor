<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Nodes\ParagraphNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class ParagraphNodeRenderer implements NodeRenderer
{
    /** @var ParagraphNode */
    private $paragraphNode;

    /** @var Renderer */
    private $renderer;

    public function __construct(ParagraphNode $paragraphNode)
    {
        $this->paragraphNode = $paragraphNode;
        $this->renderer = $paragraphNode->getEnvironment()->getRenderer();
    }

    public function render() : string
    {
        return $this->renderer->render('paragraph.html.twig', [
            'paragraphNode' => $this->paragraphNode,
        ]);
    }
}
