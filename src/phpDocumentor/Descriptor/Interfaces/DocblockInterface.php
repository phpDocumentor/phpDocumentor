<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\TagDescriptor;

interface DocblockInterface
{
    /**
     * Sets a summary describing this element.
     */
    public function setSummary(string $summary): void;

    /**
     * Returns the summary describing this element.
     */
    public function getSummary(): string;

    /**
     * Returns all tags associated with this element.
     *
     * @return Collection<Collection<TagDescriptor>>
     */
    public function getTags(): Collection;
}
