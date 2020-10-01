<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Nodes;

use phpDocumentor\Guides\RestructuredText\Environment;

class TitleNode extends Node
{
    /** @var SpanNode */
    protected $value;

    /** @var int */
    protected $level;

    /** @var string */
    protected $token;

    /** @var string */
    protected $id;

    /** @var string */
    protected $target = '';

    public function __construct(Node $value, int $level, string $token)
    {
        parent::__construct($value);

        $this->level = $level;
        $this->token = $token;
        $this->id    = Environment::slugify($this->value->getValue());
    }

    public function getValue() : SpanNode
    {
        return $this->value;
    }

    public function getLevel() : int
    {
        return $this->level;
    }

    public function setTarget(string $target) : void
    {
        $this->target = $target;
    }

    public function getTarget() : string
    {
        return $this->target;
    }

    public function getId() : string
    {
        return $this->id;
    }
}
