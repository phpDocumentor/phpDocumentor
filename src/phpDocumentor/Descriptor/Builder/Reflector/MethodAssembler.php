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

use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Reflection\ClassReflector\MethodReflector;

/**
 * Assembles a MethodDescriptor from a MethodReflector.
 */
class MethodAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param MethodReflector $data
     *
     * @return MethodDescriptor
     */
    public function create($data)
    {
        $methodDescriptor = new MethodDescriptor();
        $methodDescriptor->setFullyQualifiedStructuralElementName($data->getName() . '()');
        $methodDescriptor->setName($data->getShortName());
        $methodDescriptor->setVisibility($data->getVisibility() ?: 'public');
        $methodDescriptor->setFinal($data->isFinal());
        $methodDescriptor->setAbstract($data->isAbstract());
        $methodDescriptor->setStatic($data->isStatic());

        $this->assembleDocBlock($data->getDocBlock(), $methodDescriptor);

        foreach ($data->getArguments() as $argument) {
            $argumentAssembler  = new ArgumentAssembler();
            $argumentDescriptor = $argumentAssembler->create(
                $argument,
                $methodDescriptor->getTags()->get('param', array())
            );
            $methodDescriptor->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
        }

        $methodDescriptor->setLine($data->getLinenumber());

        return $methodDescriptor;
    }
}
