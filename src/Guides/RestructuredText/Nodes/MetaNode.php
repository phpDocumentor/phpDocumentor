<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Nodes;

class MetaNode extends Node
{
    /** @var string */
    protected $key;

    /** @var string */
    protected $value;

    public function __construct(string $key, string $value)
    {
        parent::__construct();

        $this->key   = $key;
        $this->value = $value;
    }

    public function getKey() : string
    {
        return $this->key;
    }

    public function getValue() : string
    {
        return $this->value;
    }
}
