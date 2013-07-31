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

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Tag\MethodDescriptor;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\MethodTag;

class MethodAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param MethodTag $data
     *
     * @return MethodDescriptor
     */
    public function create($data)
    {
        $descriptor = new MethodDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setMethodName($data->getMethodName());

        $response = new ReturnDescriptor('return');
        $response->setTypes($data->getTypes());
        $descriptor->setResponse($response);

        foreach ($data->getArguments() as $argument) {
            if (count($argument) > 1) {
                list($argumentType, $argumentName) = $argument;
            } else {
                $argumentName = current($argument);
                $argumentType = 'mixed';
            }
            $argumentDescriptor = new ArgumentDescriptor();
            $argumentDescriptor->setTypes(array($argumentType));
            $argumentDescriptor->setName($argumentName);
            $descriptor->getArguments()->set($argumentName, $argumentDescriptor);
        }

        return $descriptor;
    }
}
