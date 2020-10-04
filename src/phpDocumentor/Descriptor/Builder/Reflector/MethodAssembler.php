<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Method;
use function array_key_exists;
use function end;
use function strrpos;
use function substr;

/**
 * Assembles a MethodDescriptor from a MethodReflector.
 *
 * @extends AssemblerAbstract<MethodDescriptor, Method>
 */
class MethodAssembler extends AssemblerAbstract
{
    /** @var ArgumentAssembler */
    protected $argumentAssembler;

    /**
     * Initializes this assembler with its dependencies.
     */
    public function __construct(ArgumentAssembler $argumentAssembler)
    {
        $this->argumentAssembler = $argumentAssembler;
    }

    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Method $data
     */
    public function create(object $data) : MethodDescriptor
    {
        $methodDescriptor = new MethodDescriptor();
        $methodDescriptor->setNamespace(
            substr(
                (string) $data->getFqsen(),
                0,
                strrpos((string) $data->getFqsen(), '\\')
            )
        );
        $this->mapReflectorToDescriptor($data, $methodDescriptor);

        $this->assembleDocBlock($data->getDocBlock(), $methodDescriptor);
        $this->addArguments($data, $methodDescriptor);
        $this->addVariadicArgument($data, $methodDescriptor);

        return $methodDescriptor;
    }

    /**
     * Maps the fields to the reflector to the descriptor.
     */
    protected function mapReflectorToDescriptor(Method $reflector, MethodDescriptor $descriptor) : void
    {
        $descriptor->setFullyQualifiedStructuralElementName($reflector->getFqsen());
        $descriptor->setName($reflector->getName());
        $descriptor->setVisibility((string) $reflector->getVisibility() ?: 'public');
        $descriptor->setFinal($reflector->isFinal());
        $descriptor->setAbstract($reflector->isAbstract());
        $descriptor->setStatic($reflector->isStatic());
        $descriptor->setLine($reflector->getLocation()->getLineNumber());
        $descriptor->setReturnType($reflector->getReturnType());
    }

    /**
     * Adds the reflected Arguments to the Descriptor.
     */
    protected function addArguments(Method $reflector, MethodDescriptor $descriptor) : void
    {
        foreach ($reflector->getArguments() as $argument) {
            $this->addArgument($argument, $descriptor);
        }
    }

    /**
     * Adds a single reflected Argument to the Method Descriptor.
     */
    protected function addArgument(Argument $argument, MethodDescriptor $descriptor) : void
    {
        /** @var Collection<ParamDescriptor> $params */
        $params = $descriptor->getTags()->fetch('param', new Collection())->filter(ParamDescriptor::class);

        if (!$this->argumentAssembler->getBuilder()) {
            $this->argumentAssembler->setBuilder($this->builder);
        }

        $argumentDescriptor = $this->argumentAssembler->create($argument, $params);
        $argumentDescriptor->setLine($descriptor->getLine());

        $descriptor->addArgument($argumentDescriptor->getName(), $argumentDescriptor);
    }

    /**
     * Checks if there is a variadic argument in the `@param` tags and adds it to the list of Arguments in
     * the Descriptor unless there is already one present.
     */
    protected function addVariadicArgument(Method $data, MethodDescriptor $methodDescriptor) : void
    {
        if (!$data->getDocBlock()) {
            return;
        }

        $paramTags = $data->getDocBlock()->getTagsByName('param');

        /** @var Param|InvalidTag|bool $lastParamTag */
        $lastParamTag = end($paramTags);
        if (!$lastParamTag instanceof Param) {
            return;
        }

        if (!$lastParamTag->isVariadic()
            || !array_key_exists($lastParamTag->getVariableName(), $methodDescriptor->getArguments()->getAll())
        ) {
            return;
        }

        $types = $lastParamTag->getType();

        $argument = new ArgumentDescriptor();
        $argument->setName($lastParamTag->getVariableName());
        $argument->setType($types);
        $argument->setDescription(new DescriptionDescriptor($lastParamTag->getDescription(), []));
        $argument->setLine($methodDescriptor->getLine());
        $argument->setVariadic(true);

        $methodDescriptor->getArguments()->set($argument->getName(), $argument);
    }
}
