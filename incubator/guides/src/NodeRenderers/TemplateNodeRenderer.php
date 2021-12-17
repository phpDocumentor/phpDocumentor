<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\NodeRenderers;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Renderer;

final class TemplateNodeRenderer implements NodeRenderer
{
    /** @var Renderer */
    private $renderer;

    /** @var string */
    private $template;

    /** @var string */
    private $nodeClass;

    /** @param class-string<Node> $nodeClass */
    public function __construct(Renderer $renderer, string $template, string $nodeClass)
    {
        $this->renderer = $renderer;
        $this->template = $template;
        $this->nodeClass = $nodeClass;
    }

    public function supports(Node $node): bool
    {
        return $node instanceof $this->nodeClass;
    }

    public function render(Node $node, RenderContext $environment): string
    {
        return $this->renderer->render(
            $this->template,
            ['node' => $node]
        );
    }
}
