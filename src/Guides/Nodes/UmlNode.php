<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes;

class UmlNode extends Node
{
    /**
     * @param string[] $lines
     */
    public function __construct(string $lines)
    {
        parent::__construct($lines);
    }
}
