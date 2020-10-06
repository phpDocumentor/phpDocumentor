<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Nodes\MetaNode;
use phpDocumentor\Guides\Renderers\NodeRenderer;
use phpDocumentor\Guides\TemplateRenderer;

class MetaNodeRenderer implements NodeRenderer
{
    /** @var MetaNode */
    private $metaNode;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(MetaNode $metaNode, TemplateRenderer $templateRenderer)
    {
        $this->metaNode         = $metaNode;
        $this->templateRenderer = $templateRenderer;
    }

    public function render() : string
    {
        return $this->templateRenderer->render('meta.html.twig', [
            'metaNode' => $this->metaNode,
        ]);
    }
}
