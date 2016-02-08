<?php

namespace phpDocumentor\Application\Renderer\Template\Action;

use phpDocumentor\DomainModel\Renderer\Renderer;
use phpDocumentor\DomainModel\Renderer\Template\Action;
use phpDocumentor\DomainModel\Renderer\Template\ActionHandler;

final class JsonpHandler implements ActionHandler
{
    /** @var Renderer */
    private $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param Action|Xml $action
     */
    public function __invoke(Action $action)
    {
        // TODO: Finish this
        // $this->renderer->render();
    }
}
