<?php

namespace phpDocumentor\DomainModel\Renderer;

use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\Views\View;

interface Renderer
{
    public function render(View $view, Path $destination, $template = null);
}
