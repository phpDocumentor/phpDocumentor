<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration\Definition;

interface Normalizable
{
    /**
     * @param array<mixed> $configuration
     *
     * @return array<mixed>
     */
    public function normalize(array $configuration): array;
}
