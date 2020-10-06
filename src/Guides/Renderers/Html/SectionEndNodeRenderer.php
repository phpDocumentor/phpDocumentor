<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Nodes\SectionEndNode;
use phpDocumentor\Guides\Renderers\NodeRenderer;
use phpDocumentor\Guides\TemplateRenderer;

class SectionEndNodeRenderer implements NodeRenderer
{
    /** @var SectionEndNode */
    private $sectionEndNode;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(SectionEndNode $sectionEndNode, TemplateRenderer $templateRenderer)
    {
        $this->sectionEndNode   = $sectionEndNode;
        $this->templateRenderer = $templateRenderer;
    }

    public function render() : string
    {
        return $this->templateRenderer->render('section-end.html.twig', [
            'sectionEndNode' => $this->sectionEndNode,
        ]);
    }
}
