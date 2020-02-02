<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration\Definition;

interface Normalizable
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @return array<string, array<mixed>>
     */
    public function normalize(array $configuration) : array;
}
