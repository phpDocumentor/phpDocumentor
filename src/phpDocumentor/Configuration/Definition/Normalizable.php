<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration\Definition;

use phpDocumentor\Dsn;

interface Normalizable
{
    public function normalize(array $configuration, ?Dsn $uri) : array;
}
