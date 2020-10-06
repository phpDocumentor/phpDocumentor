<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers;

interface NodeRenderer
{
    public function render() : string;
}
