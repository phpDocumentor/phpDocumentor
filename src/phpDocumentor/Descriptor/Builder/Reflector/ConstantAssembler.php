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

use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Reflection\ConstantReflector;

/**
 * Assembles a ConstantDescriptor from a ConstantReflector.
 */
class ConstantAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param ConstantReflector $data
     *
     * @return ConstantDescriptor
     */
    public function create($data)
    {
        $constantDescriptor = new ConstantDescriptor();
        $constantDescriptor->setName($data->getShortName());
        $constantDescriptor->setValue($data->getValue());
        // Reflection library formulates namespace as global but this is not wanted for phpDocumentor itself
        $constantDescriptor->setNamespace(
            '\\' . (strtolower($data->getNamespace()) == 'global' ? '' :$data->getNamespace())
        );
        $constantDescriptor->setFullyQualifiedStructuralElementName(
            (trim($constantDescriptor->getNamespace(), '\\') ? $constantDescriptor->getNamespace() : '')
            . '\\' . $data->getShortName()
        );

        $this->assembleDocBlock($data->getDocBlock(), $constantDescriptor);

        $constantDescriptor->setLine($data->getLinenumber());

        return $constantDescriptor;
    }
}
