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

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Constant;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\Property;
use phpDocumentor\Reflection\Php\Trait_;

use function strlen;
use function substr;

/**
 * Assembles an TraitDescriptor using an TraitReflector.
 *
 * @extends AssemblerAbstract<TraitInterface, Trait_>
 */
class TraitAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Trait_ $data
     */
    public function buildDescriptor(object $data): TraitInterface
    {
        $traitDescriptor = new TraitDescriptor();

        $traitDescriptor->setFullyQualifiedStructuralElementName($data->getFqsen());
        $traitDescriptor->setName($data->getName());
        $traitDescriptor->setStartLocation($data->getLocation());
        $traitDescriptor->setEndLocation($data->getEndLocation());
        $traitDescriptor->setPackage($this->extractPackageFromDocBlock($data->getDocBlock()) ?? '');

        // Reflection library formulates namespace as global but this is not wanted for phpDocumentor itself
        $traitDescriptor->setNamespace(
            substr((string) $data->getFqsen(), 0, -strlen($data->getName()) - 1),
        );

        $this->assembleDocBlock($data->getDocBlock(), $traitDescriptor);

        $this->addConstants($data->getConstants(), $traitDescriptor);
        $this->addProperties($data->getProperties(), $traitDescriptor);
        $this->addMethods($data->getMethods(), $traitDescriptor);
        $this->addUses($data->getUsedTraits(), $traitDescriptor);

        return $traitDescriptor;
    }

    /**
     * Registers the child constants with the generated Class Descriptor.
     *
     * @param Constant[] $constants
     */
    protected function addConstants(array $constants, TraitDescriptor $traitDescriptor): void
    {
        foreach ($constants as $constant) {
            $constantDescriptor = $this->getBuilder()->buildDescriptor($constant, ConstantDescriptor::class);
            if ($constantDescriptor === null) {
                continue;
            }

            $constantDescriptor->setParent($traitDescriptor);
            $traitDescriptor->getConstants()->set($constantDescriptor->getName(), $constantDescriptor);
        }
    }

    /**
     * Registers the child properties with the generated Trait Descriptor.
     *
     * @param Property[] $properties
     */
    protected function addProperties(array $properties, TraitInterface $traitDescriptor): void
    {
        foreach ($properties as $property) {
            $propertyDescriptor = $this->getBuilder()->buildDescriptor($property, PropertyDescriptor::class);
            if ($propertyDescriptor === null) {
                continue;
            }

            $propertyDescriptor->setParent($traitDescriptor);
            $traitDescriptor->getProperties()->set($propertyDescriptor->getName(), $propertyDescriptor);
        }
    }

    /**
     * Registers the child methods with the generated Trait Descriptor.
     *
     * @param Method[] $methods
     */
    protected function addMethods(array $methods, TraitInterface $traitDescriptor): void
    {
        foreach ($methods as $method) {
            $methodDescriptor = $this->getBuilder()->buildDescriptor($method, MethodDescriptor::class);
            if ($methodDescriptor === null) {
                continue;
            }

            $methodDescriptor->setParent($traitDescriptor);
            $traitDescriptor->getMethods()->set($methodDescriptor->getName(), $methodDescriptor);
        }
    }

    /**
     * Registers traits used by this trait.
     *
     * @param Fqsen[] $usedTraits
     */
    protected function addUses(array $usedTraits, TraitInterface $traitDescriptor): void
    {
        $traits = $traitDescriptor->getUsedTraits();
        foreach ($usedTraits as $traitFqsen) {
            $traits->set((string) $traitFqsen, $traitFqsen);
        }
    }
}
