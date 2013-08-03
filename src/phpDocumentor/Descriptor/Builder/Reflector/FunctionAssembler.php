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

use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Reflection\FunctionReflector;

/**
 * Assembles a FunctionDescriptor from a FunctionReflector.
 */
class FunctionAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param FunctionReflector $data
     *
     * @return FunctionDescriptor
     */
    public function create($data)
    {
        $functionDescriptor = new FunctionDescriptor();
        $functionDescriptor->setFullyQualifiedStructuralElementName($data->getName() . '()');
        $functionDescriptor->setName($data->getShortName());
        $functionDescriptor->setLine($data->getLinenumber());

        // Reflection library formulates namespace as global but this is not wanted for phpDocumentor itself
        $functionDescriptor->setNamespace(
            '\\' . (strtolower($data->getNamespace()) == 'global' ? '' :$data->getNamespace())
        );

        $this->assembleDocBlock($data->getDocBlock(), $functionDescriptor);

        foreach ($data->getArguments() as $argument) {
            $argumentAssembler  = new ArgumentAssembler();
            $argumentDescriptor = $argumentAssembler->create(
                $argument,
                $functionDescriptor->getTags()->get('param', array())
            );
            $functionDescriptor->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
        }

        return $functionDescriptor;
    }
}
