<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Nodes;

class BlockNode extends Node
{
    /** @var string */
    protected $value;

    /**
     * @param string[] $lines
     */
    public function __construct(array $lines)
    {
        parent::__construct($this->normalizeLines($lines));
    }

    public function getValue() : string
    {
        return $this->value;
    }
}
