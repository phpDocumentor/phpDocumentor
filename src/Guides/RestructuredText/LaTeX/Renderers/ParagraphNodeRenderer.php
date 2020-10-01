<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\LaTeX\Renderers;

use phpDocumentor\Guides\RestructuredText\Nodes\ParagraphNode;
use phpDocumentor\Guides\RestructuredText\Renderers\NodeRenderer;
use phpDocumentor\Guides\RestructuredText\Templates\TemplateRenderer;

class ParagraphNodeRenderer implements NodeRenderer
{
    /** @var ParagraphNode */
    private $paragraphNode;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(ParagraphNode $paragraphNode, TemplateRenderer $templateRenderer)
    {
        $this->paragraphNode    = $paragraphNode;
        $this->templateRenderer = $templateRenderer;
    }

    public function render() : string
    {
        return $this->templateRenderer->render('paragraph.tex.twig', [
            'paragraphNode' => $this->paragraphNode,
        ]);
    }
}
