<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Nodes;

class CallableNode extends Node
{
    /** @var callable */
    private $callable;

    public function __construct(callable $callable)
    {
        parent::__construct();

        $this->callable = $callable;
    }

    public function getCallable() : callable
    {
        return $this->callable;
    }
}
