<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Reflection\ClassReflector\MethodReflector;
use phpDocumentor\Reflection\ConstantReflector;
use phpDocumentor\Reflection\InterfaceReflector;

/**
 * Assembles an InterfaceDescriptor using an InterfaceReflector.
 */
class InterfaceAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param InterfaceReflector $data
     *
     * @return InterfaceDescriptor
     */
    public function create($data)
    {
        $interfaceDescriptor = new InterfaceDescriptor();

        $interfaceDescriptor->setFullyQualifiedStructuralElementName($data->getName());
        $interfaceDescriptor->setName($data->getShortName());
        $interfaceDescriptor->setLine($data->getLinenumber());
        $interfaceDescriptor->setPackage($this->extractPackageFromDocBlock($data->getDocBlock()) ?: '');

        // Reflection library formulates namespace as global but this is not wanted for phpDocumentor itself
        $interfaceDescriptor->setNamespace(
            '\\' . (strtolower($data->getNamespace()) == 'global' ? '' :$data->getNamespace())
        );

        $this->assembleDocBlock($data->getDocBlock(), $interfaceDescriptor);
        $this->addConstants($data->getConstants(), $interfaceDescriptor);
        $this->addMethods($data->getMethods(), $interfaceDescriptor);

        foreach ($data->getParentInterfaces() as $interfaceClassName) {
            $interfaceDescriptor->getParent()->set($interfaceClassName, $interfaceClassName);
        }

        return $interfaceDescriptor;
    }

    /**
     * Registers the child constants with the generated Interface Descriptor.
     *
     * @param ConstantReflector[] $constants
     * @param InterfaceDescriptor $interfaceDescriptor
     *
     * @return void
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
     * @param MethodReflector[] $methods
     * @param InterfaceDescriptor $interfaceDescriptor
     *
     * @return void
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
