<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes;

class RawNode extends Node
{
    /** @var callable */
    protected $value;

    protected function doRender(): string
    {
        $value = $this->value;

        return $value();
    }
}
