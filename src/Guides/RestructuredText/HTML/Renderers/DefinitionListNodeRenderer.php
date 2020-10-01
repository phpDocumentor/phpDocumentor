<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML\Renderers;

use phpDocumentor\Guides\RestructuredText\Nodes\DefinitionListNode;
use phpDocumentor\Guides\RestructuredText\Renderers\NodeRenderer;
use phpDocumentor\Guides\RestructuredText\Templates\TemplateRenderer;

class DefinitionListNodeRenderer implements NodeRenderer
{
    /** @var DefinitionListNode */
    private $definitionListNode;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(DefinitionListNode $definitionListNode, TemplateRenderer $templateRenderer)
    {
        $this->definitionListNode = $definitionListNode;
        $this->templateRenderer   = $templateRenderer;
    }

    public function render() : string
    {
        return $this->templateRenderer->render('definition-list.html.twig', [
            'definitionListNode' => $this->definitionListNode,
            'definitionList' => $this->definitionListNode->getDefinitionList(),
        ]);
    }
}
