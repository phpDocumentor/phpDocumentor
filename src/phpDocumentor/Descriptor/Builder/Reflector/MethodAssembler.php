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

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Reflection\ClassReflector\MethodReflector;
use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;
use phpDocumentor\Reflection\FunctionReflector\ArgumentReflector;

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
        $methodDescriptor->setLine($data->getLinenumber());

        $this->assembleDocBlock($data->getDocBlock(), $methodDescriptor);
        $this->addArguments($data, $methodDescriptor);
        $this->addVariadicArgument($data, $methodDescriptor);

        return $methodDescriptor;
    }

    /**
     * Adds the reflected Arguments to the Descriptor.
     *
     * @param MethodReflector  $data
     * @param MethodDescriptor $methodDescriptor
     *
     * @return void
     */
    protected function addArguments($data, $methodDescriptor)
    {
        foreach ($data->getArguments() as $argument) {
            $this->addArgument($argument, $methodDescriptor);
        }
    }

    /**
     * Adds a single reflected Argument to the Method Descriptor.
     *
     * @param ArgumentReflector $argument
     * @param MethodDescriptor  $methodDescriptor
     *
     * @return void
     */
    protected function addArgument($argument, $methodDescriptor)
    {
        $argumentAssembler = new ArgumentAssembler();
        $argumentDescriptor = $argumentAssembler->create(
            $argument,
            $methodDescriptor->getTags()->get('param', array())
        );
        $methodDescriptor->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
    }

    /**
     * Checks if there is a variadic argument in the `@param` tags and adds it to the list of Arguments in
     * the Descriptor unless there is already one present.
     *
     * @param MethodReflector  $data
     * @param MethodDescriptor $methodDescriptor
     *
     * @return void
     */
    protected function addVariadicArgument($data, $methodDescriptor)
    {
        if (!$data->getDocBlock()) {
            return;
        }

        $paramTags = $data->getDocBlock()->getTagsByName('param');

        /** @var ParamTag $lastParamTag */
        $lastParamTag = end($paramTags);

        if (!$lastParamTag) {
            return;
        }

        if ($lastParamTag->isVariadic()
            && !in_array($lastParamTag->getVariableName(), array_keys($methodDescriptor->getArguments()->getAll()))
        ) {
            $argument = new ArgumentDescriptor();
            $argument->setName($lastParamTag->getVariableName());
            $argument->setTypes($lastParamTag->getTypes());
            $argument->setDescription($lastParamTag->getDescription());
            $argument->setLine($methodDescriptor->getLine());
            $argument->setVariadic(true);

            $methodDescriptor->getArguments()->set($argument->getName(), $argument);
        }
    }
}
