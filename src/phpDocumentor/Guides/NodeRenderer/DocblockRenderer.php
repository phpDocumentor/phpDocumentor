<?php

namespace phpDocumentor\Guides\NodeRenderer;

use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\DocblockNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\TemplateRenderer;

class DocblockRenderer implements NodeRenderer
{
    public function __construct(private readonly TemplateRenderer $renderer)
    {
    }

    public function supports(Node $node): bool
    {
        return $node instanceof DocblockNode;
    }

    public function render(Node $node, RenderContext $renderContext): string
    {
        if ($node->getDescriptor() === null) {
            return '';
        }

        return $this->renderer->renderTemplate(
            $renderContext,
            'components/method.html.twig',
            ['method' => $node->getDescriptor()],
        );
    }
}
