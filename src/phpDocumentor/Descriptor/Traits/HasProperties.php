<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Traits;

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\ChildInterface;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\PropertyInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;

trait HasProperties
{
    /** @var Collection<PropertyInterface> $properties References to properties defined in this class. */
    protected Collection $properties;

    /**
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<PropertyInterface> $properties
     */
    public function setProperties(Collection $properties): void
    {
        $this->properties = $properties;
    }

    /** @return Collection<PropertyInterface> */
    public function getProperties(): Collection
    {
        if (! isset($this->properties)) {
            $this->properties = Collection::fromInterfaceString(PropertyInterface::class);
        }

        return $this->properties;
    }

    /**
     * @return Collection<PropertyInterface>
     *
     * @todo check whether this function works properly, the business logic feels off somehow
     */
    public function getInheritedProperties(): Collection
    {
        $inheritedProperties = Collection::fromInterfaceString(PropertyInterface::class);

        if ($this instanceof ClassInterface) {
            foreach ($this->getUsedTraits() as $trait) {
                if (! $trait instanceof TraitInterface) {
                    continue;
                }

                $inheritedProperties = $inheritedProperties->merge($trait->getProperties());
            }
        }

        if ($this instanceof ChildInterface && $this->getParent() instanceof ClassInterface === false) {
            return $inheritedProperties;
        }

        $inheritedProperties = $inheritedProperties->merge($this->getParent()->getProperties());

        return $inheritedProperties->merge($this->getParent()->getInheritedProperties());
    }
}
