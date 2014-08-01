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

use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Reflection\ClassReflector\PropertyReflector;

/**
 * Assembles a PropertyDescriptor from a PropertyReflector.
 */
class PropertyAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param PropertyReflector $data
     *
     * @return PropertyDescriptor
     */
    public function create($data)
    {
        $propertyDescriptor = new PropertyDescriptor();
        $propertyDescriptor->setFullyQualifiedStructuralElementName($data->getName());
        $propertyDescriptor->setName($data->getShortName());
        $propertyDescriptor->setVisibility($data->getVisibility() ?: 'public');
        $propertyDescriptor->setStatic($data->isStatic());
        $propertyDescriptor->setDefault($data->getDefault());

        $this->assembleDocBlock($data->getDocBlock(), $propertyDescriptor);
        $propertyDescriptor->setLine($data->getLinenumber());

        return $propertyDescriptor;
    }
}
