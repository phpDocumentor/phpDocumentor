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

namespace phpDocumentor\Descriptor;

use InvalidArgumentException;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Descriptor\Validation\Error;
use function ltrim;
use function sprintf;

/**
 * Descriptor representing a Trait.
 *
 * @api
 * @package phpDocumentor\AST
 */
class TraitDescriptor extends DescriptorAbstract implements Interfaces\TraitInterface
{
    /** @var Collection<PropertyDescriptor> $properties */
    protected $properties;

    /** @var Collection<MethodDescriptor> $methods */
    protected $methods;

    /** @var Collection<TraitDescriptor|string> $usedTraits */
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
        return new Collection();
    }

    /**
     * @return Collection<MethodDescriptor>
     */
    public function getMagicMethods() : Collection
    {
        /** @var Collection<Tag\MethodDescriptor> $methodTags */
        $methodTags = $this->getTags()->fetch('method', new Collection());

        $methods = Collection::fromClassString(MethodDescriptor::class);

        /** @var Tag\MethodDescriptor $methodTag */
        foreach ($methodTags as $methodTag) {
            $method = new MethodDescriptor();
            $method->setName($methodTag->getMethodName());
            $method->setDescription($methodTag->getDescription());
            $method->setStatic($methodTag->isStatic());
            $method->setParent($this);

            /** @var Collection<ReturnDescriptor> $returnTags */
            $returnTags = $method->getTags()->fetch('return', new Collection());
            $returnTags->add($methodTag->getResponse());

            foreach ($methodTag->getArguments() as $name => $argument) {
                $method->addArgument($name, $argument);
            }

            $methods->add($method);
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
        return new Collection();
    }

    /**
     * @return Collection<PropertyDescriptor>
     */
    public function getMagicProperties() : Collection
    {
        $tags = $this->getTags();
        /** @var Collection<Tag\PropertyDescriptor> $propertyTags */
        $propertyTags = $tags->fetch('property', new Collection())->filter(Tag\PropertyDescriptor::class)
            ->merge($tags->fetch('property-read', new Collection())->filter(Tag\PropertyDescriptor::class))
            ->merge($tags->fetch('property-write', new Collection())->filter(Tag\PropertyDescriptor::class));

        $properties = Collection::fromClassString(PropertyDescriptor::class);

        /** @var Tag\PropertyDescriptor $propertyTag */
        foreach ($propertyTags as $propertyTag) {
            $property = new PropertyDescriptor();
            $property->setName(ltrim($propertyTag->getVariableName(), '$'));
            $property->setDescription($propertyTag->getDescription());
            $property->setType($propertyTag->getType());
            try {
                $property->setParent($this);
            } catch (InvalidArgumentException $e) {
                $property->getErrors()->add(
                    new Error(
                        'ERROR',
                        sprintf(
                            'Property name is invalid %s',
                            $e->getMessage()
                        ),
                        null
                    )
                );
            }

            $properties->add($property);
        }

        return $properties;
    }

    /**
     * @param PackageDescriptor|string $package
     */
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
     *
     * @param Collection<TraitDescriptor|string> $usedTraits
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
     * @return Collection<TraitDescriptor|string>
     */
    public function getUsedTraits() : Collection
    {
        return $this->usedTraits;
    }
}
