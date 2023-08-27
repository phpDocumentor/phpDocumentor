<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Reflection\Metadata\Metadata;

interface MetadataAwareInterface
{
    /** @param Metadata[] $metadata */
    public function setMetadata(array $metadata): void;

    /** @return Metadata[] */
    public function getMetadata(): array;
}
