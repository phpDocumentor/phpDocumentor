<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Nodes;

class SeparatorNode extends Node
{
    /** @var int */
    protected $level;

    public function __construct(int $level)
    {
        parent::__construct();

        $this->level = $level;
    }

    public function getLevel() : int
    {
        return $this->level;
    }
}
