<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Renderers;

interface NodeRenderer
{
    public function render() : string;
}
