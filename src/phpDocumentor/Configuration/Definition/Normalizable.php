<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration\Definition;

interface Normalizable
{
    public function normalize(array $configuration): array;
}
