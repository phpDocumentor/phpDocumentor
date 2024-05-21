<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\AttributeInterface;

trait HasAttributes
{
    /** @var Collection<AttributeInterface> $attributes Attributes set on this class. */
    protected Collection $attributes;

    /** @internal should not be called by any other class than the assamblers */
    public function addAttribute(AttributeInterface $attribute): void
    {
        if (! isset($this->attributes)) {
            $this->attributes = Collection::fromInterfaceString(AttributeInterface::class);
        }

        $this->getAttributes()->add($attribute);
    }

    /** @return Collection<AttributeInterface> */
    public function getAttributes(): Collection
    {
        if (! isset($this->attributes)) {
            $this->attributes = Collection::fromInterfaceString(AttributeInterface::class);
        }

        return $this->attributes;
    }
}
