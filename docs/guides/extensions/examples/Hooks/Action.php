<?php

declare(strict_types=1);

namespace phpDocumentor\WordpressHooks\Reflection\Php;

use phpDocumentor\Reflection\DocBlock;

final class Action
{
    public function __construct(private readonly string $name, private readonly DocBlock $docBlock)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDocBlock(): DocBlock
    {
        return $this->docBlock;
    }
}
