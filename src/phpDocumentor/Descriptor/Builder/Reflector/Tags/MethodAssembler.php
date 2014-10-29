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

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Tag\MethodDescriptor;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\MethodTag;
use phpDocumentor\Reflection\DocBlock\Type\Collection;

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
        $response->setTypes($this->builder->buildDescriptor(new Collection($data->getTypes())));
        $descriptor->setResponse($response);

        foreach ($data->getArguments() as $argument) {
            $argumentDescriptor = $this->createArgumentDescriptorForMagicMethod($argument);
            $descriptor->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
        }

        return $descriptor;
    }

    /**
     * Construct an argument descriptor given the array representing an argument with a Method Tag in the Reflection
     * component.
     *
     * @param string[] $argument
     *
     * @return ArgumentDescriptor
     */
    private function createArgumentDescriptorForMagicMethod($argument)
    {
        $argumentType = null;
        $argumentName = null;
        $argumentDefault = false; // false means we have not encountered the '=' yet.
        foreach ($argument as $part) {
            $part = trim($part);
            if (!$part) {
                continue;
            }

            if (!$argumentType && $part[0] != '$') {
                $argumentType = $part;
            } elseif (!$argumentName) {
                $argumentName = $part;
            } elseif ($argumentName && !$argumentType) {
                $argumentType = $part;
            } elseif ($part == '=') {
                $argumentDefault = null;
            } elseif ($argumentDefault === null) {
                $argumentDefault = $part;
            }
        }
        if ($argumentDefault === false) {
            $argumentDefault = null;
        }

        // if no name is set but a type is then the input is malformed and we correct for it
        if ($argumentType && !$argumentName) {
            $argumentName = $argumentType;
            $argumentType = null;
        }

        // if there is no type then we assume it is 'mixed'
        if (!$argumentType) {
            $argumentType = 'mixed';
        }

        $argumentDescriptor = new ArgumentDescriptor();
        $argumentDescriptor->setTypes($this->builder->buildDescriptor(new Collection(array($argumentType))));
        $argumentDescriptor->setName($argumentName[0] == '$' ? $argumentName : '$' . $argumentName);
        $argumentDescriptor->setDefault($argumentDefault);

        return $argumentDescriptor;
    }
}
