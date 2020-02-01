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
     * @var DescriptorAbstract|Fqsen|string|null $parent
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

    /** @var Collection<?ConstantDescriptor> $constants References to constants defined in this class. */
    protected $constants;

    /** @var Collection<?PropertyDescriptor> $properties References to properties defined in this class. */
    protected $properties;

    /** @var Collection<?MethodDescriptor> $methods References to methods defined in this class. */
    protected $methods;

    /** @var Collection<TraitDescriptor> $usedTraits References to traits consumed by this class */
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
     * @param DescriptorAbstract|Fqsen|string|null $parents
     */
    public function setParent($parents) : void
    {
        $this->parent = $parents;
    }

    /**
     * @return DescriptorAbstract|Fqsen|string|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritDoc}
     */
    public function setInterfaces(Collection $implements) : void
    {
        $this->implements = $implements;
    }

    /**
     * {@inheritDoc}
     */
    public function getInterfaces() : Collection
    {
        return $this->implements;
    }

    /**
     * {@inheritDoc}
     */
    public function setFinal(bool $final) : void
    {
        $this->final = $final;
    }

    /**
     * {@inheritDoc}
     */
    public function isFinal() : bool
    {
        return $this->final;
    }

    /**
     * {@inheritDoc}
     */
    public function setAbstract(bool $abstract) : void
    {
        $this->abstract = $abstract;
    }

    /**
     * {@inheritDoc}
     */
    public function isAbstract() : bool
    {
        return $this->abstract;
    }

    /**
     * {@inheritDoc}
     */
    public function setConstants(Collection $constants) : void
    {
        $this->constants = $constants;
    }

    /**
     * {@inheritDoc}
     */
    public function getConstants() : Collection
    {
        return $this->constants;
    }

    /**
     * {@inheritDoc}
     */
    public function getInheritedConstants() : Collection
    {
        if ($this->getParent() === null || (!$this->getParent() instanceof self)) {
            return new Collection();
        }

        $inheritedConstants = clone $this->getParent()->getConstants();

        return $inheritedConstants->merge($this->getParent()->getInheritedConstants());
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

    public function getMagicMethods() : Collection
    {
        /** @var Collection $methodTags */
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

    public function getMagicProperties() : Collection
    {
        /** @var Collection $propertyTags */
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
            // TODO #840: Workaround; for some reason there are NULLs in the constants array.
            if (!$constant) {
                continue;
            }

            $constant->setPackage($package);
        }

        foreach ($this->getProperties() as $property) {
            // TODO #840: Workaround; for some reason there are NULLs in the properties array.
            if (!$property) {
                continue;
            }

            $property->setPackage($package);
        }

        foreach ($this->getMethods() as $method) {
            // TODO #840: Workaround; for some reason there are NULLs in the methods array.
            if (!$method) {
                continue;
            }

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

    /**
     * @return ClassDescriptor|Fqsen|null
     */
    public function getInheritedElement()
    {
        return $this->getParent();
    }
}
