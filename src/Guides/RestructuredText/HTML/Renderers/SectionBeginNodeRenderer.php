<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML\Renderers;

use phpDocumentor\Guides\RestructuredText\Nodes\SectionBeginNode;
use phpDocumentor\Guides\RestructuredText\Renderers\NodeRenderer;
use phpDocumentor\Guides\RestructuredText\Templates\TemplateRenderer;

class SectionBeginNodeRenderer implements NodeRenderer
{
    /** @var SectionBeginNode */
    private $sectionBeginNode;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(SectionBeginNode $sectionBeginNode, TemplateRenderer $templateRenderer)
    {
        $this->sectionBeginNode = $sectionBeginNode;
        $this->templateRenderer = $templateRenderer;
    }

    public function render() : string
    {
        return $this->templateRenderer->render('section-begin.html.twig', [
            'sectionBeginNode' => $this->sectionBeginNode,
        ]);
    }
}
