<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder;

use function is_a;

/**
 * @template T
 */
final class Matcher
{
    /** @var class-string<T> */
    private $type;

    /**
     * @param class-string<SelfT> $type
     *
     * @return self<SelfT>
     *
     * @template SelfT
     */
    public static function forType(string $type) : self
    {
        return new self($type);
    }

    /**
     * @param object|class-string $criteria
     *
     * @psalm-assert-if-true T|class-string<T> $criteria
     */
    public function __invoke($criteria) : bool
    {
        return is_a($criteria, $this->type, true);
    }

    /**
     * @param class-string<T> $type
     */
    private function __construct(string $type)
    {
        $this->type = $type;
    }
}
