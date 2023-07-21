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

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\EnumCaseDescriptor;
use phpDocumentor\Descriptor\EnumDescriptor;
use phpDocumentor\Descriptor\Interfaces\EnumInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Enum_;
use phpDocumentor\Reflection\Php\EnumCase;
use phpDocumentor\Reflection\Php\Method;

use function strlen;
use function substr;

/**
 * Assembles an EnumDescriptor using an ClassReflector.
 *
 * @extends AssemblerAbstract<EnumInterface, Enum_>
 */
final class EnumAssembler extends AssemblerAbstract
{
    /** @param Enum_ $data */
    protected function buildDescriptor(object $data): EnumInterface
    {
        $descriptor = new EnumDescriptor();

        $descriptor->setFullyQualifiedStructuralElementName($data->getFqsen());
        $descriptor->setName($data->getName());
        $descriptor->setPackage(
            $this->extractPackageFromDocBlock($data->getDocBlock()) ?? $this->getBuilder()->getDefaultPackageName(),
        );
        $descriptor->setStartLocation($data->getLocation());
        $descriptor->setEndLocation($data->getEndLocation());

        $descriptor->setNamespace(substr((string) $data->getFqsen(), 0, -strlen($data->getName()) - 1));
        $descriptor->setBackedType($data->getBackedType());

        $interfaces = $descriptor->getInterfaces();
        foreach ($data->getInterfaces() as $interfaceClassName) {
            $interfaces->set((string) $interfaceClassName, $interfaceClassName);
        }

        $this->assembleDocBlock($data->getDocBlock(), $descriptor);
        $this->addCases($data->getCases(), $descriptor);
        $this->addMethods($data->getMethods(), $descriptor);
        $this->addUses($data->getUsedTraits(), $descriptor);

        return $descriptor;
    }

    /**
     * Registers the child methods with the generated Class Descriptor.
     *
     * @param Method[] $methods
     */
    private function addMethods(array $methods, EnumInterface $descriptor): void
    {
        foreach ($methods as $method) {
            $methodDescriptor = $this->getBuilder()->buildDescriptor($method, MethodDescriptor::class);
            if ($methodDescriptor === null) {
                continue;
            }

            $methodDescriptor->setParent($descriptor);
            $descriptor->getMethods()->set($methodDescriptor->getName(), $methodDescriptor);
        }
    }

    /**
     * Registers the used traits with the generated Class Descriptor.
     *
     * @param array<Fqsen> $traits
     */
    private function addUses(array $traits, EnumInterface $descriptor): void
    {
        /** @var Collection<TraitInterface|Fqsen> $usedTraits */
        $usedTraits = new Collection($traits);

        $descriptor->setUsedTraits($usedTraits);
    }

    /** @param EnumCase[] $cases */
    private function addCases(array $cases, EnumInterface $descriptor): void
    {
        foreach ($cases as $case) {
            $caseDescriptor = $this->getBuilder()->buildDescriptor($case, EnumCaseDescriptor::class);
            if ($caseDescriptor === null) {
                continue;
            }

            $caseDescriptor->setParent($descriptor);
            $descriptor->getCases()->set($caseDescriptor->getName(), $caseDescriptor);
        }
    }
}
