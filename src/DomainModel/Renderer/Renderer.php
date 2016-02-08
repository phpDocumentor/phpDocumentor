<?php

namespace phpDocumentor\DomainModel\Renderer;

use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\ReadModel\ReadModel;

interface Renderer
{
    public function render(ReadModel $view, Path $destination, $template = null);
}
