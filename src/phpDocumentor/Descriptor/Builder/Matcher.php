<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder;

use function is_a;

final class Matcher
{
    /** @var string */
    private $type;

    public static function forType(string $type) : self
    {
        return new static($type);
    }

    /**
     * @param object|string $criteria
     */
    public function __invoke($criteria) : bool
    {
        return is_a($criteria, $this->type, true);
    }

    private function __construct(string $type)
    {
        $this->type = $type;
    }
}
