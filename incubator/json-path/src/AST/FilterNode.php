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

use ArrayIterator;
use CallbackFilterIterator;
use InvalidArgumentException;
use IteratorIterator;
use phpDocumentor\JsonPath\Executor;

use function is_array;
use function is_iterable;
use function iterator_to_array;

final class FilterNode implements PathNode
{
    private Expression $expression;

    public function __construct(Expression $expression)
    {
        $this->expression = $expression;
    }

    /** @inheritDoc */
    public function visit(Executor $param, $currentObject, $root)
    {
        if (is_iterable($currentObject) === false) {
            throw new InvalidArgumentException('Can only filter iteratable values %s given');
        }

        $valueIterator = new CallbackFilterIterator(
            is_array($currentObject) ? new ArrayIterator($currentObject) : new IteratorIterator($currentObject),
            fn ($current) => $this->expression->visit($param, $current, $root)
        );

        return iterator_to_array($valueIterator, false);
    }
}
