<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration;

use phpDocumentor\FileSystem\Path;

class TemplateDefinition
{
    /** @param array<string, mixed> $parameters */
    public function __construct(
        public string $name,
        public Path|null $location = null,
        public array $parameters = [],
    ) {
    }
}
