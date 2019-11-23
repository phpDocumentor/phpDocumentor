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

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Constant;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\Property;

/**
 * Assembles an ClassDescriptor using an ClassReflector.
 */
class ClassAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Class_ $data
     *
     * @return ClassDescriptor
     */
    public function create($data)
    {
        $classDescriptor = new ClassDescriptor();

        $classDescriptor->setFullyQualifiedStructuralElementName($data->getFqsen());
        $classDescriptor->setName($data->getName());
        $classDescriptor->setPackage(
            $this->extractPackageFromDocBlock($data->getDocBlock()) ?: $this->getBuilder()->getDefaultPackage()
        );
        $classDescriptor->setLine($data->getLocation()->getLineNumber());
        $classDescriptor->setParent($data->getParent());
        $classDescriptor->setAbstract($data->isAbstract());
        $classDescriptor->setFinal($data->isFinal());
        $classDescriptor->setNamespace(substr((string) $data->getFqsen(), 0, -strlen($data->getName()) - 1));

        foreach ($data->getInterfaces() as $interfaceClassName) {
            $classDescriptor->getInterfaces()->set((string) $interfaceClassName, $interfaceClassName);
        }

        $this->assembleDocBlock($data->getDocBlock(), $classDescriptor);

        $this->addConstants($data->getConstants(), $classDescriptor);
        $this->addProperties($data->getProperties(), $classDescriptor);
        $this->addMethods($data->getMethods(), $classDescriptor);
        $this->addUses($data->getUsedTraits(), $classDescriptor);

        return $classDescriptor;
    }

    /**
     * Registers the child constants with the generated Class Descriptor.
     *
     * @param Constant[] $constants
     */
    protected function addConstants(array $constants, ClassDescriptor $classDescriptor): void
    {
        foreach ($constants as $constant) {
            $constantDescriptor = $this->getBuilder()->buildDescriptor($constant);
            if ($constantDescriptor instanceof ConstantDescriptor) {
                $constantDescriptor->setParent($classDescriptor);
                $classDescriptor->getConstants()->set($constantDescriptor->getName(), $constantDescriptor);
            }
        }
    }

    /**
     * Registers the child properties with the generated Class Descriptor.
     *
     * @param Property[] $properties
     */
    protected function addProperties(array $properties, ClassDescriptor $classDescriptor): void
    {
        foreach ($properties as $property) {
            $propertyDescriptor = $this->getBuilder()->buildDescriptor($property);
            if ($propertyDescriptor instanceof PropertyDescriptor) {
                $propertyDescriptor->setParent($classDescriptor);
                $classDescriptor->getProperties()->set($propertyDescriptor->getName(), $propertyDescriptor);
            }
        }
    }

    /**
     * Registers the child methods with the generated Class Descriptor.
     *
     * @param Method[] $methods
     */
    protected function addMethods(array $methods, ClassDescriptor $classDescriptor): void
    {
        foreach ($methods as $method) {
            $methodDescriptor = $this->getBuilder()->buildDescriptor($method);
            if ($methodDescriptor instanceof MethodDescriptor) {
                $methodDescriptor->setParent($classDescriptor);
                $classDescriptor->getMethods()->set($methodDescriptor->getName(), $methodDescriptor);
            }
        }
    }

    /**
     * Registers the used traits with the generated Class Descriptor.
     *
     * @param string[] $traits
     */
    protected function addUses(array $traits, ClassDescriptor $classDescriptor): void
    {
        $classDescriptor->setUsedTraits(new Collection($traits));
    }
}
