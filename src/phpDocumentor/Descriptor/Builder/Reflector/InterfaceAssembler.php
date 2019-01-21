<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Reflection\Php\Constant;
use phpDocumentor\Reflection\Php\Interface_;
use phpDocumentor\Reflection\Php\Method;

/**
 * Assembles an InterfaceDescriptor using an InterfaceReflector.
 */
class InterfaceAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Interface_ $data
     *
     * @return InterfaceDescriptor
     */
    public function create($data)
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

        foreach ($data->getParents() as $interfaceClassName) {
            $interfaceDescriptor->getParent()->set((string) $interfaceClassName, $interfaceClassName);
        }

        return $interfaceDescriptor;
    }

    /**
     * Registers the child constants with the generated Interface Descriptor.
     *
     * @param Constant[] $constants
     */
    protected function addConstants(array $constants, InterfaceDescriptor $interfaceDescriptor): void
    {
        foreach ($constants as $constant) {
            $constantDescriptor = $this->getBuilder()->buildDescriptor($constant);
            if ($constantDescriptor instanceof ConstantDescriptor) {
                $constantDescriptor->setParent($interfaceDescriptor);
                $interfaceDescriptor->getConstants()->set($constantDescriptor->getName(), $constantDescriptor);
            }
        }
    }

    /**
     * Registers the child methods with the generated Interface Descriptor.
     *
     * @param Method[] $methods
     */
    protected function addMethods(array $methods, InterfaceDescriptor $interfaceDescriptor): void
    {
        foreach ($methods as $method) {
            $methodDescriptor = $this->getBuilder()->buildDescriptor($method);
            if ($methodDescriptor instanceof MethodDescriptor) {
                $methodDescriptor->setParent($interfaceDescriptor);
                $interfaceDescriptor->getMethods()->set($methodDescriptor->getName(), $methodDescriptor);
            }
        }
    }
}
