<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\UmlNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Transformer\Writer\Graph\PlantumlRenderer;

class UmlNodeRenderer implements NodeRenderer
{
    /** @var Renderer */
    private $renderer;

    /** @var PlantumlRenderer */
    private $plantumlRenderer;

    public function __construct(
        PlantumlRenderer $plantumlRenderer,
        Renderer $renderer
    ) {
        $this->renderer = $renderer;
        $this->plantumlRenderer = $plantumlRenderer;
    }

    public function render(Node $node) : string
    {
        if ($node instanceof UmlNode === false) {
            throw new \InvalidArgumentException('Invalid node presented');
        }


        return $this->renderer
            ->render(
                'uml.html.twig',
                [
                    'umlNode' => $node,
                    'svg' => $this->plantumlRenderer->render($node->getValue()) ?: '',
                ]
            );
    }
}
