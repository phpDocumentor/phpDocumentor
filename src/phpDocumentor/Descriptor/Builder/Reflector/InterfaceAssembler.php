<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Reflection\InterfaceReflector;
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
        $interfaceDescriptor->setNamespace(substr($data->getFqsen(), 0, -strlen($data->getName()) - 1));

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
     * @param InterfaceDescriptor $interfaceDescriptor
     */
    protected function addConstants($constants, $interfaceDescriptor)
    {
        foreach ($constants as $constant) {
            $constantDescriptor = $this->getBuilder()->buildDescriptor($constant);
            if ($constantDescriptor) {
                $constantDescriptor->setParent($interfaceDescriptor);
                $interfaceDescriptor->getConstants()->set($constantDescriptor->getName(), $constantDescriptor);
            }
        }
    }

    /**
     * Registers the child methods with the generated Interface Descriptor.
     *
     * @param Method[] $methods
     * @param InterfaceDescriptor $interfaceDescriptor
     */
    protected function addMethods($methods, $interfaceDescriptor)
    {
        foreach ($methods as $method) {
            $methodDescriptor = $this->getBuilder()->buildDescriptor($method);
            if ($methodDescriptor) {
                $methodDescriptor->setParent($interfaceDescriptor);
                $interfaceDescriptor->getMethods()->set($methodDescriptor->getName(), $methodDescriptor);
            }
        }
    }
}
