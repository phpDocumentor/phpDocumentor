<?php

declare(strict_types=1);

namespace phpDocumentor\Example\Nodes;

use phpDocumentor\Guides\Nodes\AbstractNode;

/** @extends AbstractNode<string> */
final class HelloNode extends AbstractNode
{
    public function __construct(string $name)
    {
        $this->value = $name;
    }

    public function getName(): string
    {
        return $this->value;
    }
}
