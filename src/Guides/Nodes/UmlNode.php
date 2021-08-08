<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes;

class UmlNode extends Node
{
    /** @var string */
    private $caption = '';

    public function setCaption(string $caption): void
    {
        $this->caption = $caption;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }
}
