<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use InvalidArgumentException;
use phpDocumentor\Descriptor\Tag\BaseTypes\TypedVariableAbstract;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Reflection\Fqsen;
use function ltrim;
use function sprintf;

/**
 * Descriptor representing a Class.
 */
class ClassDescriptor extends DescriptorAbstract implements Interfaces\ClassInterface
{
    /**
     * Reference to an instance of the superclass for this class, if any.
     *
     * @var ClassDescriptor|InterfaceDescriptor|Fqsen|string|null $parent
     */
    protected $parent;

    /**
     * References to interfaces that are implemented by this class.
     *
     * @var Collection<InterfaceDescriptor|Fqsen> $implements
     */
    protected $implements;

    /** @var bool $abstract Whether this is an abstract class. */
    protected $abstract = false;

    /** @var bool $final Whether this class is marked as final and can't be subclassed. */
    protected $final = false;

    /** @var Collection<ConstantDescriptor> $constants References to constants defined in this class. */
    protected $constants;

    /** @var Collection<PropertyDescriptor> $properties References to properties defined in this class. */
    protected $properties;

    /** @var Collection<MethodDescriptor> $methods References to methods defined in this class. */
    protected $methods;

    /** @var Collection<TraitDescriptor>|Collection<Fqsen> $usedTraits References to traits consumed by this class */
    protected $usedTraits;

    /**
     * Initializes the all properties representing a collection with a new Collection object.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setInterfaces(new Collection());
        $this->setUsedTraits(new Collection());
        $this->setConstants(new Collection());
        $this->setProperties(new Collection());
        $this->setMethods(new Collection());
    }

    /**
     * @param ClassDescriptor|InterfaceDescriptor|Fqsen|string|null $parents
     */
    public function setParent($parents) : void
    {
        $this->parent = $parents;
    }

    /**
     * @return ClassDescriptor|InterfaceDescriptor|Fqsen|string|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setInterfaces(Collection $implements) : void
    {
        $this->implements = $implements;
    }

    public function getInterfaces() : Collection
    {
        return $this->implements;
    }

    public function setFinal(bool $final) : void
    {
        $this->final = $final;
    }

    public function isFinal() : bool
    {
        return $this->final;
    }

    public function setAbstract(bool $abstract) : void
    {
        $this->abstract = $abstract;
    }

    public function isAbstract() : bool
    {
        return $this->abstract;
    }

    public function setConstants(Collection $constants) : void
    {
        $this->constants = $constants;
    }

    public function getConstants() : Collection
    {
        return $this->constants;
    }

    /**
     * @return Collection<ConstantDescriptor>
     */
    public function getInheritedConstants() : Collection
    {
        if ($this->getParent() === null || (!$this->getParent() instanceof self)) {
            return new Collection();
        }

        $inheritedConstants = clone $this->getParent()->getConstants();

        return $inheritedConstants->merge($this->getParent()->getInheritedConstants());
    }

    public function setMethods(Collection $methods) : void
    {
        $this->methods = $methods;
    }

    public function getMethods() : Collection
    {
        return $this->methods;
    }

    public function getInheritedMethods() : Collection
    {
        /** @var Collection<MethodDescriptor> $inheritedMethods */
        $inheritedMethods = new Collection();

        foreach ($this->getUsedTraits() as $trait) {
            if (!$trait instanceof TraitDescriptor) {
                continue;
            }

            $inheritedMethods = $inheritedMethods->merge(clone $trait->getMethods());
        }

        if ($this->getParent() === null || (!$this->getParent() instanceof self)) {
            return $inheritedMethods;
        }

        $inheritedMethods = $inheritedMethods->merge(clone $this->getParent()->getMethods());

        return $inheritedMethods->merge($this->getParent()->getInheritedMethods());
    }

    /**
     * @return Collection<MethodDescriptor>
     */
    public function getMagicMethods() : Collection
    {
        /** @var Collection<MethodDescriptor> $methodTags */
        $methodTags = clone $this->getTags()->get('method', new Collection());

        /** @var Collection<MethodDescriptor> $methods */
        $methods = new Collection();

        /** @var Tag\MethodDescriptor $methodTag */
        foreach ($methodTags as $methodTag) {
            $method = new MethodDescriptor();
            $method->setName($methodTag->getMethodName());
            $method->setDescription($methodTag->getDescription());
            $method->setStatic($methodTag->isStatic());
            $method->setParent($this);

            /** @var Collection<ReturnDescriptor> $returnTags */
            $returnTags = $method->getTags()->get('return', new Collection());
            $returnTags->add($methodTag->getResponse());

            foreach ($methodTag->getArguments() as $name => $argument) {
                $method->addArgument($name, $argument);
            }

            $methods->add($method);
        }

        if ($this->getParent() instanceof static) {
            $methods = $methods->merge($this->getParent()->getMagicMethods());
        }

        return $methods;
    }

    public function setProperties(Collection $properties) : void
    {
        $this->properties = $properties;
    }

    public function getProperties() : Collection
    {
        return $this->properties;
    }

    public function getInheritedProperties() : Collection
    {
        /** @var Collection<PropertyDescriptor> $inheritedProperties */
        $inheritedProperties = new Collection();

        foreach ($this->getUsedTraits() as $trait) {
            if (!$trait instanceof TraitDescriptor) {
                continue;
            }

            $inheritedProperties = $inheritedProperties->merge(clone $trait->getProperties());
        }

        if ($this->getParent() === null || (!$this->getParent() instanceof self)) {
            return $inheritedProperties;
        }

        $inheritedProperties = $inheritedProperties->merge(clone $this->getParent()->getProperties());

        return $inheritedProperties->merge($this->getParent()->getInheritedProperties());
    }

    /**
     * @return Collection<PropertyDescriptor>
     */
    public function getMagicProperties() : Collection
    {
        /** @var Collection<Tag\PropertyDescriptor> $propertyTags */
        $propertyTags = clone $this->getTags()->get('property', new Collection());
        $propertyTags = $propertyTags->merge($this->getTags()->get('property-read', new Collection()));
        $propertyTags = $propertyTags->merge($this->getTags()->get('property-write', new Collection()));

        /** @var Collection<PropertyDescriptor> $properties */
        $properties = new Collection();

        try {
            /** @var Tag\PropertyDescriptor $propertyTag */
            foreach ($propertyTags as $propertyTag) {
                if (!$propertyTag instanceof TypedVariableAbstract) {
                    continue;
                }

                $property = new PropertyDescriptor();
                $property->setName(ltrim($propertyTag->getVariableName(), '$'));
                $property->setDescription($propertyTag->getDescription());
                $property->setType($propertyTag->getType());
                $property->setParent($this);

                $properties->add($property);
            }
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(
                sprintf(
                    'Failed to get magic properties from "%s": %s',
                    $this->getFullyQualifiedStructuralElementName(),
                    $e->getMessage()
                ),
                0,
                $e
            );
        }

        if ($this->getParent() instanceof self) {
            $properties = $properties->merge($this->getParent()->getMagicProperties());
        }

        return $properties;
    }

    /**
     * @inheritDoc
     */
    public function setPackage($package) : void
    {
        parent::setPackage($package);

        foreach ($this->getConstants() as $constant) {
            $constant->setPackage($package);
        }

        foreach ($this->getProperties() as $property) {
            $property->setPackage($package);
        }

        foreach ($this->getMethods() as $method) {
            $method->setPackage($package);
        }
    }

    /**
     * Sets a collection of all traits used by this class.
     *
     * @param Collection<TraitDescriptor>|Collection<Fqsen> $usedTraits
     */
    public function setUsedTraits(Collection $usedTraits) : void
    {
        $this->usedTraits = $usedTraits;
    }

    /**
     * Returns the traits used by this class.
     *
     * Returned values may either be a string (when the Trait is not in this project) or a TraitDescriptor.
     *
     * @return Collection<TraitDescriptor>|Collection<Fqsen>
     */
    public function getUsedTraits() : Collection
    {
        return $this->usedTraits;
    }

    /**
     * @return ClassDescriptor|Fqsen|null
     */
    public function getInheritedElement() : ?object
    {
        return $this->getParent();
    }
}
