<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Traits;

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

use InvalidArgumentException;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\ChildInterface;
use phpDocumentor\Descriptor\Interfaces\Collection as CollectionInterface;
use phpDocumentor\Descriptor\Interfaces\PropertyInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\Tag;
use phpDocumentor\Descriptor\Validation\Error;

use function ltrim;
use function method_exists;
use function sprintf;

trait HasProperties
{
    /** @var CollectionInterface<PropertyInterface> $properties References to properties defined in this class. */
    protected CollectionInterface $properties;

    /**
     * @internal should not be called by any other class than the assamblers
     *
     * @param CollectionInterface<PropertyInterface> $properties
     */
    public function setProperties(CollectionInterface $properties): void
    {
        $this->properties = $properties;
    }

    /** @return CollectionInterface<PropertyInterface> */
    public function getProperties(): CollectionInterface
    {
        if (! isset($this->properties)) {
            $this->properties = Collection::fromInterfaceString(PropertyInterface::class);
        }

        return $this->properties;
    }

    /**
     * @return CollectionInterface<PropertyInterface>
     *
     * @todo check whether this function works properly, the business logic feels off somehow
     */
    public function getInheritedProperties(): CollectionInterface
    {
        $inheritedProperties = Collection::fromInterfaceString(PropertyInterface::class);

        if (method_exists($this, 'getUsedTraits')) {
            foreach ($this->getUsedTraits() as $trait) {
                if (! $trait instanceof TraitInterface) {
                    continue;
                }

                $inheritedProperties = $inheritedProperties->merge($trait->getProperties());
            }
        }

        if ($this instanceof ChildInterface === false) {
            return $inheritedProperties;
        }

        $parent = $this->getParent();
        if ($parent instanceof self === false) {
            return $inheritedProperties;
        }

        $inheritedProperties = $inheritedProperties->merge(
            $parent->getProperties()->matches(
                static fn (PropertyInterface $property) => (string) $property->getVisibility() !== 'private',
            ),
        );

        return $inheritedProperties->merge($parent->getInheritedProperties());
    }

    /** @return CollectionInterface<PropertyInterface> */
    public function getMagicProperties(): CollectionInterface
    {
        $tags = $this->getTags();
        /** @var CollectionInterface<Tag\PropertyDescriptor> $propertyTags */
        $propertyTags = $tags->fetch('property', new Collection())->filter(Tag\PropertyDescriptor::class)
            ->merge($tags->fetch('property-read', new Collection())->filter(Tag\PropertyDescriptor::class))
            ->merge($tags->fetch('property-write', new Collection())->filter(Tag\PropertyDescriptor::class));

        $properties = Collection::fromInterfaceString(PropertyInterface::class);

        /** @var Tag\PropertyDescriptor $propertyTag */
        foreach ($propertyTags as $propertyTag) {
            $property = new PropertyDescriptor();
            $property->setName(ltrim($propertyTag->getVariableName(), '$'));
            $property->setDescription($propertyTag->getDescription());
            $property->setType($propertyTag->getType());
            $property->setWriteOnly($propertyTag->getName() === 'property-write');
            $property->setReadOnly($propertyTag->getName() === 'property-read');
            try {
                $property->setParent($this);
                $properties->set($property->getName(), $property);
            } catch (InvalidArgumentException $e) {
                $this->errors->add(
                    new Error(
                        'ERROR',
                        sprintf(
                            'Property name is invalid %s',
                            $e->getMessage(),
                        ),
                        null,
                    ),
                );
            }
        }

        if ($this instanceof ChildInterface === false) {
            return $properties;
        }

        $parent = $this->getParent();
        if ($parent instanceof self) {
            $properties = $properties->merge($parent->getMagicProperties());
        }

        return $properties;
    }
}
