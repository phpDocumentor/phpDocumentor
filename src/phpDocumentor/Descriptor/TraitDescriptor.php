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
use phpDocumentor\Descriptor\Interfaces\MethodInterface;
use phpDocumentor\Descriptor\Interfaces\PropertyInterface;
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
    use Traits\HasProperties;
    use Traits\HasMethods;
    use Traits\UsesTraits;
    use Traits\HasConstants;

    /** @return Collection<MethodInterface> */
    public function getInheritedMethods(): Collection
    {
        return Collection::fromInterfaceString(MethodInterface::class);
    }

    /** @return Collection<MethodInterface> */
    public function getMagicMethods(): Collection
    {
        /** @var Collection<Tag\MethodDescriptor> $methodTags */
        $methodTags = $this->getTags()->fetch('method', new Collection());

        $methods = Collection::fromInterfaceString(MethodInterface::class);

        /** @var Tag\MethodDescriptor $methodTag */
        foreach ($methodTags as $methodTag) {
            $method = new MethodDescriptor();
            $method->setName($methodTag->getMethodName());
            $method->setDescription($methodTag->getDescription());
            $method->setStatic($methodTag->isStatic());
            $method->setParent($this);
            $method->setReturnType($methodTag->getResponse()->getType());
            $method->setHasReturnByReference($methodTag->getHasReturnByReference());

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

    /** @return Collection<PropertyInterface> */
    public function getInheritedProperties(): Collection
    {
        return Collection::fromInterfaceString(PropertyInterface::class);
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

        return $properties;
    }
}
