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

use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
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
        $constantDescriptor->setNamespace('\\' . $data->getNamespace());
        $constantDescriptor->setFullyQualifiedStructuralElementName(
            $constantDescriptor->getNamespace() . '\\' . $data->getShortName()
        );

        $this->assembleDocBlock($data->getDocBlock(), $constantDescriptor);

        $constantDescriptor->setLocation('', $data->getLinenumber());

        return $constantDescriptor;
    }
}
