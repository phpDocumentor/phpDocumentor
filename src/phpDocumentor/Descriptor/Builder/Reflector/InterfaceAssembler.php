<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\InterfaceDescriptor;
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
        $interfaceDescriptor->setNamespace('\\' . $data->getNamespace());

        $this->assembleDocBlock($data, $interfaceDescriptor);

        $interfaceDescriptor->setLocation('', $data->getLinenumber());

        foreach ($data->getParentInterfaces() as $interfaceClassName) {
            $interfaceDescriptor->getParent()->set($interfaceClassName, $interfaceClassName);
        }

        return $interfaceDescriptor;
    }
}
