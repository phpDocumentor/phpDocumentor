<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Nodes\SectionBeginNode;
use phpDocumentor\Guides\Renderers\NodeRenderer;
use phpDocumentor\Guides\TemplateRenderer;

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
