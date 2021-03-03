<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers;

use phpDocumentor\Guides\Nodes\UmlNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Transformer\Writer\Graph\PlantumlRenderer;

class UmlNodeRenderer implements NodeRenderer
{
    /** @var UmlNode */
    private $umlNode;

    /** @var Renderer */
    private $renderer;

    /** @var PlantumlRenderer */
    private $plantumlRenderer;

    public function __construct(UmlNode $umlNode, PlantumlRenderer $plantumlRenderer)
    {
        $this->umlNode  = $umlNode;
        $this->renderer = $umlNode->getEnvironment()->getRenderer();
        $this->plantumlRenderer = $plantumlRenderer;
    }

    public function render(): string
    {
        return $this->umlNode->getEnvironment()->getRenderer()
            ->render(
                'uml.html.twig',
                [
                    'umlNode' => $this->umlNode,
                    'svg' => $this->plantumlRenderer->render($this->umlNode->getValue()) ?: '',
                ]
            );
    }
}
