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
use phpDocumentor\Descriptor\EnumDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Enum_;
use phpDocumentor\Reflection\Php\Method;

use function strlen;
use function substr;

/**
 * Assembles an EnumDescriptor using an ClassReflector.
 *
 * @extends AssemblerAbstract<EnumDescriptor, Enum_>
 */
final class EnumAssembler extends AssemblerAbstract
{
    protected function buildDescriptor(object $data): EnumDescriptor
    {
        $descriptor = new EnumDescriptor();

        $descriptor->setFullyQualifiedStructuralElementName($data->getFqsen());
        $descriptor->setName($data->getName());
        $descriptor->setPackage(
            $this->extractPackageFromDocBlock($data->getDocBlock()) ?? $this->getBuilder()->getDefaultPackage()
        );
        $descriptor->setLine($data->getLocation()->getLineNumber());

        $descriptor->setNamespace(substr((string) $data->getFqsen(), 0, -strlen($data->getName()) - 1));

        $interfaces = $descriptor->getInterfaces();
        foreach ($data->getInterfaces() as $interfaceClassName) {
            $interfaces->set((string) $interfaceClassName, $interfaceClassName);
        }

        $this->assembleDocBlock($data->getDocBlock(), $descriptor);
        $this->addMethods($data->getMethods(), $descriptor);
        $this->addUses($data->getUsedTraits(), $descriptor);

        return $descriptor;
    }

    /**
     * Registers the child methods with the generated Class Descriptor.
     *
     * @param Method[] $methods
     */
    private function addMethods(array $methods, EnumDescriptor $descriptor): void
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
    private function addUses(array $traits, EnumDescriptor $descriptor): void
    {
        $descriptor->setUsedTraits(new Collection($traits));
    }
}
