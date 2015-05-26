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
use phpDocumentor\Reflection\DocBlock\Type\Collection;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Reflection\ClassReflector\MethodReflector;
use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;
use phpDocumentor\Reflection\FunctionReflector\ArgumentReflector;

/**
 * Assembles a MethodDescriptor from a MethodReflector.
 */
class MethodAssembler extends AssemblerAbstract
{
    /** @var ArgumentAssembler */
    protected $argumentAssembler;

    /**
     * Initializes this assembler with its dependencies.
     *
     * @param ArgumentAssembler $argumentAssembler
     */
    public function __construct(ArgumentAssembler $argumentAssembler)
    {
        $this->argumentAssembler = $argumentAssembler;
    }

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
        $this->mapReflectorToDescriptor($data, $methodDescriptor);

        $this->assembleDocBlock($data->getDocBlock(), $methodDescriptor);
        $this->addArguments($data, $methodDescriptor);
        $this->addVariadicArgument($data, $methodDescriptor);

        return $methodDescriptor;
    }

    /**
     * Maps the fields to the reflector to the descriptor.
     *
     * @param MethodReflector  $reflector
     * @param MethodDescriptor $descriptor
     *
     * @return void
     */
    protected function mapReflectorToDescriptor($reflector, $descriptor)
    {
        $descriptor->setFullyQualifiedStructuralElementName($reflector->getName() . '()');
        $descriptor->setName($reflector->getShortName());
        $descriptor->setVisibility($reflector->getVisibility() ? : 'public');
        $descriptor->setFinal($reflector->isFinal());
        $descriptor->setAbstract($reflector->isAbstract());
        $descriptor->setStatic($reflector->isStatic());
        $descriptor->setLine($reflector->getLinenumber());
    }

    /**
     * Adds the reflected Arguments to the Descriptor.
     *
     * @param MethodReflector  $reflector
     * @param MethodDescriptor $descriptor
     *
     * @return void
     */
    protected function addArguments($reflector, $descriptor)
    {
        foreach ($reflector->getArguments() as $argument) {
            $this->addArgument($argument, $descriptor);
        }
    }

    /**
     * Adds a single reflected Argument to the Method Descriptor.
     *
     * @param ArgumentReflector $argument
     * @param MethodDescriptor  $descriptor
     *
     * @return void
     */
    protected function addArgument($argument, $descriptor)
    {
        $params = $descriptor->getTags()->get('param', array());

        if (!$this->argumentAssembler->getBuilder()) {
            $this->argumentAssembler->setBuilder($this->builder);
        }
        $argumentDescriptor = $this->argumentAssembler->create($argument, $params);

        $descriptor->addArgument($argumentDescriptor->getName(), $argumentDescriptor);
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
            $types = $this->builder->buildDescriptor(new Collection($lastParamTag->getTypes()));

            $argument = new ArgumentDescriptor();
            $argument->setName($lastParamTag->getVariableName());
            $argument->setTypes($types);
            $argument->setDescription($lastParamTag->getDescription());
            $argument->setLine($methodDescriptor->getLine());
            $argument->setVariadic(true);

            $methodDescriptor->getArguments()->set($argument->getName(), $argument);
        }
    }
}
