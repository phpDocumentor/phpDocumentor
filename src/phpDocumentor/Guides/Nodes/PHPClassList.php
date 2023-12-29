<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes;

use phpDocumentor\Descriptor\Descriptor;

/** @extends AbstractNode<Descriptor[]> */
class PHPClassList extends AbstractNode
{
    public function __construct(private string $query)
    {
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}
