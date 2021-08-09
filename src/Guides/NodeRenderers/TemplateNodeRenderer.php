<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\NodeRenderers;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Renderer;

final class TemplateNodeRenderer implements NodeRenderer
{
    /** @var Renderer */
    private $renderer;

    /** @var string */
    private $template;

    public function __construct(Renderer $renderer, string $template)
    {
        $this->renderer = $renderer;
        $this->template = $template;
    }

    public function render(Node $node): string
    {
        return $this->renderer->render(
            $this->template,
            ['node' => $node]
        );
    }
}
