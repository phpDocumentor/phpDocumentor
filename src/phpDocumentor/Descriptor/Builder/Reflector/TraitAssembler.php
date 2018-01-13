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

use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\Property;
use phpDocumentor\Reflection\Php\Trait_;
use phpDocumentor\Reflection\TraitReflector;

/**
 * Assembles an TraitDescriptor using an TraitReflector.
 */
class TraitAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Trait_ $data
     *
     * @return TraitDescriptor
     */
    public function create($data)
    {
        $traitDescriptor = new TraitDescriptor();

        $traitDescriptor->setFullyQualifiedStructuralElementName($data->getFqsen());
        $traitDescriptor->setName($data->getName());
        $traitDescriptor->setLine($data->getLocation()->getLineNumber());
        $traitDescriptor->setPackage($this->extractPackageFromDocBlock($data->getDocBlock()) ?: '');

        // Reflection library formulates namespace as global but this is not wanted for phpDocumentor itself
        $traitDescriptor->setNamespace(
            substr($data->getFqsen(), 0, -strlen($data->getName()) - 1)
        );

        $this->assembleDocBlock($data->getDocBlock(), $traitDescriptor);

        $this->addProperties($data->getProperties(), $traitDescriptor);
        $this->addMethods($data->getMethods(), $traitDescriptor);

        return $traitDescriptor;
    }

    /**
     * Registers the child properties with the generated Trait Descriptor.
     *
     * @param Property[] $properties
     * @param TraitDescriptor     $traitDescriptor
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
     * @param Method[] $methods
     * @param TraitDescriptor   $traitDescriptor
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
