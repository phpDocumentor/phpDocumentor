<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration;

use phpDocumentor\FileSystem\Path;

class TemplateDefinition
{
    public function __construct(
        public string $name,
        public Path|null $location = null,
        public array $parameters = [],
    ) {
    }
}
