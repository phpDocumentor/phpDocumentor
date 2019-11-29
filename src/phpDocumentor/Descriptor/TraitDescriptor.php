<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use function ltrim;

/**
 * Descriptor representing a Trait.
 */
class TraitDescriptor extends DescriptorAbstract implements Interfaces\TraitInterface
{
    /** @var Collection $properties */
    protected $properties;

    /** @var Collection $methods */
    protected $methods;

    /** @var Collection $usedTraits */
    protected $usedTraits;

    /**
     * Initializes the all properties representing a collection with a new Collection object.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setProperties(new Collection());
        $this->setMethods(new Collection());
        $this->setUsedTraits(new Collection());
    }

    /**
     * {@inheritDoc}
     */
    public function setMethods(Collection $methods) : void
    {
        $this->methods = $methods;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethods() : Collection
    {
        return $this->methods;
    }

    /**
     * {@inheritDoc}
     */
    public function getInheritedMethods() : Collection
    {
        return new Collection();
    }

    public function getMagicMethods() : Collection
    {
        /** @var Collection $methodTags */
        $methodTags = clone $this->getTags()->get('method', new Collection());

        $methods = new Collection();

        /** @var Tag\MethodDescriptor $methodTag */
        foreach ($methodTags as $methodTag) {
            $method = new MethodDescriptor();
            $method->setName($methodTag->getMethodName());
            $method->setDescription($methodTag->getDescription());
            $method->setStatic($methodTag->isStatic());
            $method->setParent($this);

            $methods->add($method);
        }

        return $methods;
    }

    /**
     * {@inheritDoc}
     */
    public function setProperties(Collection $properties) : void
    {
        $this->properties = $properties;
    }

    /**
     * {@inheritDoc}
     */
    public function getProperties() : Collection
    {
        return $this->properties;
    }

    /**
     * {@inheritDoc}
     */
    public function getInheritedProperties() : Collection
    {
        return new Collection();
    }

    public function getMagicProperties() : Collection
    {
        /** @var Collection $propertyTags */
        $propertyTags = clone $this->getTags()->get('property', new Collection());
        $propertyTags->merge($this->getTags()->get('property-read', new Collection()));
        $propertyTags->merge($this->getTags()->get('property-write', new Collection()));

        $properties = new Collection();

        /** @var Tag\PropertyDescriptor $propertyTag */
        foreach ($propertyTags as $propertyTag) {
            $property = new PropertyDescriptor();
            $property->setName(ltrim($propertyTag->getVariableName(), '$'));
            $property->setDescription($propertyTag->getDescription());
            $property->setType($propertyTag->getType());
            $property->setParent($this);

            $properties->add($property);
        }

        return $properties;
    }

    public function setPackage($package) : void
    {
        parent::setPackage($package);

        foreach ($this->getProperties() as $property) {
            $property->setPackage($package);
        }

        foreach ($this->getMethods() as $method) {
            $method->setPackage($package);
        }
    }

    /**
     * Sets a collection of all traits used by this class.
     */
    public function setUsedTraits(Collection $usedTraits) : void
    {
        $this->usedTraits = $usedTraits;
    }

    /**
     * Returns the traits used by this class.
     *
     * Returned values may either be a string (when the Trait is not in this project) or a TraitDescriptor.
     */
    public function getUsedTraits() : Collection
    {
        return $this->usedTraits;
    }
}
