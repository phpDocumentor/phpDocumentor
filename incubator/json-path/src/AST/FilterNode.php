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

use function count;
use function current;
use function is_array;
use function is_iterable;
use function iterator_to_array;

final class FilterNode implements PathNode
{
    public function __construct(private readonly Expression $expression)
    {
    }

    /** @inheritDoc */
    public function visit(Executor $param, $currentObject, $root)
    {
        if (is_iterable($currentObject) === false) {
            throw new InvalidArgumentException('Can only filter iterable values %s given');
        }

        foreach (self::candidates($currentObject) as $current) {
            if (! $this->expression->visit($param, $current, $root)) {
                continue;
            }

            yield $current;
        }
    }

    /**
     * A field access on a single parent element forwards its sub-result as one bundled value
     * (see Executor::evaluateFieldAccess), so `$.store.books` yields the books array as a single
     * item rather than one item per book. A filter always needs the individual elements though,
     * so when the bundle turns out to be a collection itself, look inside it instead of testing
     * the whole bundle as if it were one candidate.
     *
     * @param iterable<mixed> $items
     *
     * @return iterable<mixed>
     */
    private static function candidates(iterable $items): iterable
    {
        $items = is_array($items) ? $items : iterator_to_array($items, false);

        if (count($items) === 1 && is_iterable(current($items))) {
            return self::candidates(current($items));
        }

        return $items;
    }
}
