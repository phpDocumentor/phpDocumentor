<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers;

use phpDocumentor\Guides\Nodes\UmlNode;
use phpDocumentor\Guides\Renderer;

class UmlNodeRenderer implements NodeRenderer
{
    /** @var UmlNode */
    private $umlNode;

    /** @var Renderer */
    private $renderer;

    public function __construct(UmlNode $umlNode)
    {
        $this->umlNode  = $umlNode;
        $this->renderer = $umlNode->getEnvironment()->getRenderer();
    }

    public function render(): string
    {
        return 'foo';
    }
}
