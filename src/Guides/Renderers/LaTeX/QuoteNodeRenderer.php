<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers\LaTeX;

use phpDocumentor\Guides\Nodes\QuoteNode;
use phpDocumentor\Guides\Renderers\NodeRenderer;
use phpDocumentor\Guides\TemplateRenderer;

class QuoteNodeRenderer implements NodeRenderer
{
    /** @var QuoteNode */
    private $quoteNode;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(QuoteNode $quoteNode, TemplateRenderer $templateRenderer)
    {
        $this->quoteNode        = $quoteNode;
        $this->templateRenderer = $templateRenderer;
    }

    public function render() : string
    {
        return $this->templateRenderer->render('quote.tex.twig', [
            'quoteNode' => $this->quoteNode,
        ]);
    }
}
