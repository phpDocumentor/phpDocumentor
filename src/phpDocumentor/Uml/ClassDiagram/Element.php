<?php

declare(strict_types=1);

namespace phpDocumentor\Uml\ClassDiagram;

class Element
{
    public function __construct(
        public readonly string $uml,
        public readonly bool $descriptorBased,
    ) {
    }
}
