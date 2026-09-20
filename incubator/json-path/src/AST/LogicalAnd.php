<?php

declare(strict_types=1);

namespace phpDocumentor\JsonPath\AST;

use phpDocumentor\JsonPath\Executor;

final class LogicalAnd implements Expression
{
    public function __construct(
        private readonly Expression $left,
        private readonly Expression $right,
    ) {
    }

    public function visit(Executor $param, $currentObject, $root): bool
    {
        return $param->evaluateExpression($this->left, $currentObject, $root) && $param->evaluateExpression($this->right, $currentObject, $root);
    }
}
