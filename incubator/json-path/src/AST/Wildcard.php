<?php

declare(strict_types=1);

namespace phpDocumentor\JsonPath\AST;

use phpDocumentor\JsonPath\Executor;

final class Wildcard implements Expression
{
    /** @inheritDoc */
    public function visit(Executor $param, $currentObject, $root): bool
    {
        return true;
    }

    public function getName(): string
    {
        return '*';
    }
}
