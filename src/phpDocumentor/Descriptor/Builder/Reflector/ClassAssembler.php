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

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\ConstantReflector;

/**
 * Assembles an ClassDescriptor using an ClassReflector.
 */
class ClassAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param ClassReflector $data
     *
     * @return ClassDescriptor
     */
    public function create($data)
    {
        $classDescriptor = new ClassDescriptor();

        $classDescriptor->setFullyQualifiedStructuralElementName($data->getName());
        $classDescriptor->setName($data->getShortName());
        $classDescriptor->setPackage($this->extractPackageFromDocBlock($data->getDocBlock()) ?: '');
        $classDescriptor->setLine($data->getLinenumber());
        $classDescriptor->setParent($data->getParentClass());
        $classDescriptor->setAbstract($data->isAbstract());
        $classDescriptor->setFinal($data->isFinal());

        // Reflection library formulates namespace as global but this is not wanted for phpDocumentor itself
        $classDescriptor->setNamespace(
            '\\' . (strtolower($data->getNamespace()) == 'global' ? '' :$data->getNamespace())
        );

        foreach ($data->getInterfaces() as $interfaceClassName) {
            $classDescriptor->getInterfaces()->set($interfaceClassName, $interfaceClassName);
        }

        $fqcn = $classDescriptor->getFullyQualifiedStructuralElementName();
        $namespace = substr($fqcn, 0, strrpos($fqcn, '\\'));
        $classDescriptor->setNamespace($namespace);

        $this->assembleDocBlock($data->getDocBlock(), $classDescriptor);

        $this->addConstants($data->getConstants(), $classDescriptor);
        $this->addProperties($data->getProperties(), $classDescriptor);
        $this->addMethods($data->getMethods(), $classDescriptor);
        $this->addUses($data->getTraits(), $classDescriptor);

        return $classDescriptor;
    }

    /**
     * Registers the child constants with the generated Class Descriptor.
     *
     * @param ConstantReflector[] $constants
     * @param ClassDescriptor     $classDescriptor
     *
     * @return void
     */
    protected function addConstants($constants, $classDescriptor)
    {
        foreach ($constants as $constant) {
            $constantDescriptor = $this->getBuilder()->buildDescriptor($constant);
            if ($constantDescriptor) {
                $constantDescriptor->setParent($classDescriptor);
                $classDescriptor->getConstants()->set($constantDescriptor->getName(), $constantDescriptor);
            }
        }
    }

    /**
     * Registers the child properties with the generated Class Descriptor.
     *
     * @param ClassReflector\PropertyReflector[] $properties
     * @param ClassDescriptor                    $classDescriptor
     *
     * @return void
     */
    protected function addProperties($properties, $classDescriptor)
    {
        foreach ($properties as $property) {
            $propertyDescriptor = $this->getBuilder()->buildDescriptor($property);
            if ($propertyDescriptor) {
                $propertyDescriptor->setParent($classDescriptor);
                $classDescriptor->getProperties()->set($propertyDescriptor->getName(), $propertyDescriptor);
            }
        }
    }

    /**
     * Registers the child methods with the generated Class Descriptor.
     *
     * @param ClassReflector\MethodReflector[] $methods
     * @param ClassDescriptor $classDescriptor
     *
     * @return void
     */
    protected function addMethods($methods, $classDescriptor)
    {
        foreach ($methods as $method) {
            $methodDescriptor = $this->getBuilder()->buildDescriptor($method);
            if ($methodDescriptor) {
                $methodDescriptor->setParent($classDescriptor);
                $classDescriptor->getMethods()->set($methodDescriptor->getName(), $methodDescriptor);
            }
        }
    }

    /**
     * Registers the used traits with the generated Class Descriptor.
     *
     * @param string[] $traits
     * @param ClassDescriptor $classDescriptor
     *
     * @return void
     */
    protected function addUses(array $traits, ClassDescriptor $classDescriptor)
    {
        $classDescriptor->setUsedTraits(new Collection($traits));
    }
}
