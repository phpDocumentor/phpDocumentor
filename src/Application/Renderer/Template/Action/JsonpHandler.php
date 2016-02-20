<?php

namespace phpDocumentor\Application\Renderer\Template\Action;

use phpDocumentor\Application\Renderer\JsonpRenderer;
use phpDocumentor\DomainModel\Renderer\Template\Action;
use phpDocumentor\DomainModel\Renderer\Template\ActionHandler;

final class JsonpHandler implements ActionHandler
{
    /** @var JsonpRenderer */
    private $renderer;

    public function __construct(JsonpRenderer $renderer)
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
