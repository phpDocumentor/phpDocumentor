<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\JsonPath\AST;

use InvalidArgumentException;
use phpDocumentor\JsonPath\Executor;

final class Comparison implements Expression
{
    public function __construct(
        private readonly QueryNode $left,
        private readonly string $operator,
        private readonly QueryNode $right,
    ) {
    }

    /** @inheritDoc */
    public function visit(Executor $param, $currentObject, $root): bool
    {
        return match ($this->operator) {
            '==' => $param->evaluateEqualsComparison($root, $currentObject, $this->left, $this->right),
            '!=' => $param->evaluateNotEqualsComparison($root, $currentObject, $this->left, $this->right),
            'starts_with' => $param->evaluateStartsWithComparison($root, $currentObject, $this->left, $this->right),
            'contains' => $param->evaluateContainsComparison($root, $currentObject, $this->left, $this->right),
            default => throw new InvalidArgumentException(),
        };
    }
}
