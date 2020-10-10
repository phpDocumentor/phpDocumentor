<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Nodes\QuoteNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class QuoteNodeRenderer implements NodeRenderer
{
    /** @var QuoteNode */
    private $quoteNode;

    /** @var Renderer */
    private $renderer;

    public function __construct(QuoteNode $quoteNode)
    {
        $this->quoteNode        = $quoteNode;
        $this->renderer = $quoteNode->getEnvironment()->getRenderer();
    }

    public function render() : string
    {
        return $this->renderer->render('quote.html.twig', [
            'quoteNode' => $this->quoteNode,
        ]);
    }
}
