<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Nodes;

use phpDocumentor\Guides\Nodes\Node;

class AdmonitionNode extends Node
{
    /** @var string */
    private $name;

    /** @var string */
    private $text;

    public function __construct(string $name, string $text, Node $value)
    {
        parent::__construct($value);
        $this->name = $name;
        $this->text = $text;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
