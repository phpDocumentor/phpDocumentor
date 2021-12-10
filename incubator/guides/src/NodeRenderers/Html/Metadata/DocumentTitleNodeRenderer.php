<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\NodeRenderers\Html\Metadata;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\Metadata\DocumentTitleNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Renderer;

final class DocumentTitleNodeRenderer implements NodeRenderer
{
    /** @var Renderer */
    private $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function supports(Node $node): bool
    {
        return $node instanceof DocumentTitleNode;
    }

    public function render(Node $node, Environment $environment): string
    {
        return $this->renderer->render('title.html.twig', ['title' => $node->getValueString()]);
    }
}
