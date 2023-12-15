<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline\Attribute;

use Attribute as BaseAttribute;

#[BaseAttribute(BaseAttribute::TARGET_CLASS)]
final class Stage
{
    public function __construct(
        public readonly string $name,
        public readonly int $priority = 1000,
        public readonly string|null $description = null,
    ) {
    }
}
