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
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\MethodInterface;
use phpDocumentor\Descriptor\Interfaces\PropertyInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use phpDocumentor\Descriptor\Validation\Error;
use phpDocumentor\Reflection\Fqsen;

use function ltrim;
use function sprintf;

/**
 * Descriptor representing a Class.
 *
 * @api
 * @package phpDocumentor\AST
 */
class ClassDescriptor extends DescriptorAbstract implements Interfaces\ClassInterface
{
    use Traits\CanBeFinal;
    use Traits\CanBeAbstract;
    use Traits\HasMethods;
    use Traits\HasProperties;
    use Traits\HasConstants;
    use Traits\ExtendsClass;
    use Traits\ImplementsInterfaces;
    use Traits\UsesTraits;

    /** @var bool $readOnly Whether this class is marked as readonly. */
    protected bool $readOnly = false;

    /** @internal should not be called by any other class than the assemblers */
    public function setReadOnly(bool $readOnly): void
    {
        $this->readOnly = $readOnly;
    }

    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /** @return Collection<MethodInterface> */
    public function getInheritedMethods(): Collection
    {
        $inheritedMethods = Collection::fromInterfaceString(MethodInterface::class);

        foreach ($this->getUsedTraits() as $trait) {
            if (! $trait instanceof TraitInterface) {
                continue;
            }

            $inheritedMethods = $inheritedMethods->merge($trait->getMethods());
        }

        if (! $this->getParent() instanceof self) {
            return $inheritedMethods;
        }

        $inheritedMethods = $inheritedMethods->merge($this->getParent()->getMethods());

        return $inheritedMethods->merge($this->getParent()->getInheritedMethods());
    }

    /** @return Collection<MethodInterface> */
    public function getMagicMethods(): Collection
    {
        $methodTags = $this->getTags()->fetch('method', new Collection())->filter(Tag\MethodDescriptor::class);

        $methods = Collection::fromInterfaceString(MethodInterface::class);

        foreach ($methodTags as $methodTag) {
            $method = new MethodDescriptor();
            $method->setName($methodTag->getMethodName());
            $method->setDescription($methodTag->getDescription());
            $method->setStatic($methodTag->isStatic());
            $method->setParent($this);
            $method->setReturnType($methodTag->getResponse()->getType());
            $method->setHasReturnByReference($methodTag->getHasReturnByReference());

            $returnTags = $method->getTags()->fetch('return', new Collection());
            $returnTags->add($methodTag->getResponse());

            foreach ($methodTag->getArguments() as $name => $argument) {
                $method->addArgument($name, $argument);
            }

            $methods->add($method);
        }

        $parent = $this->getParent();
        if ($parent instanceof static) {
            $methods = $methods->merge($parent->getMagicMethods());
        }

        return $methods;
    }

    /** @return Collection<PropertyInterface> */
    public function getMagicProperties(): Collection
    {
        $tags = $this->getTags();
        /** @var Collection<Tag\PropertyDescriptor> $propertyTags */
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
                $properties->add($property);
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

        $parent = $this->getParent();
        if ($parent instanceof self) {
            $properties = $properties->merge($parent->getMagicProperties());
        }

        return $properties;
    }

    /** @return ClassInterface|Fqsen|string|null */
    public function getInheritedElement()
    {
        return $this->getParent();
    }
}
