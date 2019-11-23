<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder;

final class Matcher
{
    private $type;

    public static function forType(string $type): self
    {
        return new static($type);
    }

    /** @var mixed $criteria */
    public function __invoke($criteria): bool
    {
        return is_a($criteria, $this->type, true);
    }

    private function __construct(string $type)
    {
        $this->type = $type;
    }
}
