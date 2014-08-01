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

use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\ClassReflector\MethodReflector;
use phpDocumentor\Reflection\ClassReflector\PropertyReflector;
use phpDocumentor\Reflection\TraitReflector;

/**
 * Assembles an TraitDescriptor using an TraitReflector.
 */
class TraitAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param TraitReflector $data
     *
     * @return TraitDescriptor
     */
    public function create($data)
    {
        $traitDescriptor = new TraitDescriptor();

        $traitDescriptor->setFullyQualifiedStructuralElementName($data->getName());
        $traitDescriptor->setName($data->getShortName());
        $traitDescriptor->setLine($data->getLinenumber());
        $traitDescriptor->setPackage($this->extractPackageFromDocBlock($data->getDocBlock()) ?: '');

        // Reflection library formulates namespace as global but this is not wanted for phpDocumentor itself
        $traitDescriptor->setNamespace(
            '\\' . (strtolower($data->getNamespace()) == 'global' ? '' :$data->getNamespace())
        );

        $this->assembleDocBlock($data->getDocBlock(), $traitDescriptor);

        $this->addProperties($data->getProperties(), $traitDescriptor);
        $this->addMethods($data->getMethods(), $traitDescriptor);

        return $traitDescriptor;
    }

    /**
     * Registers the child properties with the generated Trait Descriptor.
     *
     * @param PropertyReflector[] $properties
     * @param TraitDescriptor     $traitDescriptor
     *
     * @return void
     */
    protected function addProperties($properties, $traitDescriptor)
    {
        foreach ($properties as $property) {
            $propertyDescriptor = $this->getBuilder()->buildDescriptor($property);
            if ($propertyDescriptor) {
                $propertyDescriptor->setParent($traitDescriptor);
                $traitDescriptor->getProperties()->set($propertyDescriptor->getName(), $propertyDescriptor);
            }
        }
    }

    /**
     * Registers the child methods with the generated Trait Descriptor.
     *
     * @param MethodReflector[] $methods
     * @param TraitDescriptor   $traitDescriptor
     *
     * @return void
     */
    protected function addMethods($methods, $traitDescriptor)
    {
        foreach ($methods as $method) {
            $methodDescriptor = $this->getBuilder()->buildDescriptor($method);
            if ($methodDescriptor) {
                $methodDescriptor->setParent($traitDescriptor);
                $traitDescriptor->getMethods()->set($methodDescriptor->getName(), $methodDescriptor);
            }
        }
    }
}
