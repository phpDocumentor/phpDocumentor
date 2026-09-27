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

use phpDocumentor\JsonPath\Executor;

/**
 * Represents a bare path used as a filter expression, e.g. `[?(@.chapters[?(@.title == "x")])]`.
 *
 * It is truthy when evaluating the wrapped path yields at least one element.
 */
final class ExistsExpression implements Expression
{
    public function __construct(private readonly QueryNode $path)
    {
    }

    /** @inheritDoc */
    public function visit(Executor $param, $currentObject, $root): bool
    {
        return $param->evaluateExistsExpression($root, $currentObject, $this->path);
    }
}
