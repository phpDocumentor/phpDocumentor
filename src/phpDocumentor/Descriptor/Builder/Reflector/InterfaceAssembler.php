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
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Reflection\Php\Constant;
use phpDocumentor\Reflection\Php\Interface_;
use phpDocumentor\Reflection\Php\Method;
use function strlen;
use function substr;

/**
 * Assembles an InterfaceDescriptor using an InterfaceReflector.
 *
 * @extends AssemblerAbstract<InterfaceDescriptor, Interface_>
 */
class InterfaceAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Interface_ $data
     */
    public function create(object $data) : InterfaceDescriptor
    {
        $interfaceDescriptor = new InterfaceDescriptor();

        $interfaceDescriptor->setFullyQualifiedStructuralElementName($data->getFqsen());
        $interfaceDescriptor->setName($data->getName());
        $interfaceDescriptor->setLine($data->getLocation()->getLineNumber());
        $interfaceDescriptor->setPackage($this->extractPackageFromDocBlock($data->getDocBlock()) ?: '');

        // Reflection library formulates namespace as global but this is not wanted for phpDocumentor itself
        $interfaceDescriptor->setNamespace(substr((string) $data->getFqsen(), 0, -strlen($data->getName()) - 1));

        $this->assembleDocBlock($data->getDocBlock(), $interfaceDescriptor);
        $this->addConstants($data->getConstants(), $interfaceDescriptor);
        $this->addMethods($data->getMethods(), $interfaceDescriptor);

        $interfaceParent = $interfaceDescriptor->getParent();
        foreach ($data->getParents() as $interfaceClassName) {
            $interfaceParent->set((string) $interfaceClassName, $interfaceClassName);
        }

        return $interfaceDescriptor;
    }

    /**
     * Registers the child constants with the generated Interface Descriptor.
     *
     * @param Constant[] $constants
     */
    protected function addConstants(array $constants, InterfaceDescriptor $interfaceDescriptor) : void
    {
        foreach ($constants as $constant) {
            $constantDescriptor = $this->getBuilder()->buildDescriptor($constant, ConstantDescriptor::class);
            if ($constantDescriptor === null) {
                continue;
            }

            $constantDescriptor->setParent($interfaceDescriptor);
            $interfaceDescriptor->getConstants()->set($constantDescriptor->getName(), $constantDescriptor);
        }
    }

    /**
     * Registers the child methods with the generated Interface Descriptor.
     *
     * @param Method[] $methods
     */
    protected function addMethods(array $methods, InterfaceDescriptor $interfaceDescriptor) : void
    {
        foreach ($methods as $method) {
            $methodDescriptor = $this->getBuilder()->buildDescriptor($method, MethodDescriptor::class);
            if ($methodDescriptor === null) {
                continue;
            }

            $methodDescriptor->setParent($interfaceDescriptor);
            $interfaceDescriptor->getMethods()->set($methodDescriptor->getName(), $methodDescriptor);
        }
    }
}
