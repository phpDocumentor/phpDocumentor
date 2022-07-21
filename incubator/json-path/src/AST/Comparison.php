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
    private QueryNode $left;
    private string $operator;
    private QueryNode $right;

    public function __construct(QueryNode $left, string $operator, QueryNode $right)
    {
        $this->left = $left;
        $this->operator = $operator;
        $this->right = $right;
    }

    /** @inheritDoc */
    public function visit(Executor $param, $currentObject, $root): bool
    {
        switch ($this->operator) {
            case '==':
                return $param->evaluateEqualsComparison($root, $currentObject, $this->left, $this->right);
        }

        throw new InvalidArgumentException();
    }
}
