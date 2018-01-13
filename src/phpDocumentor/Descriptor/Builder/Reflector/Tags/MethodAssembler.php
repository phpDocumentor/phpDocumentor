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

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Tag\MethodDescriptor;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use phpDocumentor\Reflection\Type;

/**
 * Constructs a new descriptor from the Reflector for an `@method` tag.
 *
 * This object will read the reflected information for the `@method` tag and create a {@see MethodDescriptor} object
 * that can be used in the rest of the application and templates.
 */
class MethodAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param Method $data
     *
     * @return MethodDescriptor
     */
    public function create($data)
    {
        $descriptor = new MethodDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setMethodName($data->getMethodName());
        $descriptor->setStatic($data->isStatic());

        $response = new ReturnDescriptor('return');
        $response->setTypes($data->getReturnType());
        $descriptor->setResponse($response);

        foreach ($data->getArguments() as $argument) {
            $argumentDescriptor = $this->createArgumentDescriptorForMagicMethod($argument['name'], $argument['type']);
            $descriptor->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
        }

        return $descriptor;
    }

    /**
     * Construct an argument descriptor given the array representing an argument with a Method Tag in the Reflection
     * component.
     *
     * @param string $name
     * @return ArgumentDescriptor
     */
    private function createArgumentDescriptorForMagicMethod($name, Type $type)
    {
        $argumentDescriptor = new ArgumentDescriptor();
        $argumentDescriptor->setTypes($type);
        $argumentDescriptor->setName($name);

        return $argumentDescriptor;
    }
}
