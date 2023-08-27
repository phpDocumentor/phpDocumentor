<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Reflection\Metadata\Metadata;

trait HasMetadata
{
    /** @var Metadata[] */
    private array $metadata = [];

    /** @param Metadata[] $metadata */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    /** @return Metadata[] */
    public function getMetadata(): array
    {
        return $this->metadata;
    }
}
