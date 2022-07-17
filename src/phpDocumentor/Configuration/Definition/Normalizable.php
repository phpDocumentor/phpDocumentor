<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration\Definition;

use phpDocumentor\Dsn;
use phpDocumentor\Path;

/**
 * @template TBaseConfiguration of array
 * @template TNormalizedConfiguration of array
 */
interface Normalizable
{
    /**
     * @param TBaseConfiguration $configuration
     * @return TNormalizedConfiguration
     */
    public function normalize(array $configuration): array;
}
