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

use phpDocumentor\Descriptor\Builder\AssemblerReducer;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\MethodInterface;
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
 * @extends AssemblerAbstract<MethodInterface, Method>
 */
class MethodAssembler extends AssemblerAbstract
{
    /**
     * Initializes this assembler with its dependencies.
     */
    public function __construct(private readonly ArgumentAssembler $argumentAssembler, AssemblerReducer ...$reducers)
    {
        parent::__construct(...$reducers);
    }

    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Method $data
     */
    public function buildDescriptor(object $data): MethodInterface
    {
        $methodDescriptor = new MethodDescriptor();
        $methodDescriptor->setNamespace(
            substr(
                (string) $data->getFqsen(),
                0,
                strrpos((string) $data->getFqsen(), '\\'),
            ),
        );
        $this->mapReflectorToDescriptor($data, $methodDescriptor);

        $this->assembleDocBlock($data->getDocBlock(), $methodDescriptor);
        $this->addArguments($data, $methodDescriptor);
        $this->addVirualVariadicArgument($data, $methodDescriptor);

        return $methodDescriptor;
    }

    /**
     * Maps the fields to the reflector to the descriptor.
     */
    protected function mapReflectorToDescriptor(Method $reflector, MethodDescriptor $descriptor): void
    {
        $descriptor->setFullyQualifiedStructuralElementName($reflector->getFqsen());
        $descriptor->setName($reflector->getName());
        $descriptor->setFinal($reflector->isFinal());
        $descriptor->setAbstract($reflector->isAbstract());
        $descriptor->setStatic($reflector->isStatic());
        $descriptor->setStartLocation($reflector->getLocation());
        $descriptor->setEndLocation($reflector->getEndLocation());
        $descriptor->setReturnType($reflector->getReturnType());
        $descriptor->setHasReturnByReference($reflector->getHasReturnByReference());
    }

    /**
     * Adds the reflected Arguments to the Descriptor.
     */
    protected function addArguments(Method $reflector, MethodDescriptor $descriptor): void
    {
        foreach ($reflector->getArguments() as $argument) {
            $this->addArgument($argument, $descriptor);
        }
    }

    /**
     * Adds a single reflected Argument to the Method Descriptor.
     */
    protected function addArgument(Argument $argument, MethodDescriptor $descriptor): void
    {
        /** @var Collection<ParamDescriptor> $params */
        $params = $descriptor->getTags()->fetch('param', new Collection())->filter(ParamDescriptor::class);

        if (! $this->argumentAssembler->getBuilder()) {
            $this->argumentAssembler->setBuilder($this->builder);
        }

        $argumentDescriptor = $this->argumentAssembler->create($argument, $params);
        $argumentDescriptor->setStartLocation($descriptor->getStartLocation());
        $argumentDescriptor->setEndLocation($descriptor->getEndLocation());

        $descriptor->addArgument($argumentDescriptor->getName(), $argumentDescriptor);
    }

    /**
     * Add a virtual argument to the method descriptor if the last `@param` tag is variadic.
     *
     * Checks if there is a variadic argument in the `@param` tags and adds it to the list of Arguments in
     * the Descriptor unless there is already one present.
     */
    protected function addVirualVariadicArgument(Method $data, MethodDescriptor $methodDescriptor): void
    {
        if (! $data->getDocBlock()) {
            return;
        }

        $paramTags = $data->getDocBlock()->getTagsByName('param');

        /** @var Param|InvalidTag|bool $lastParamTag */
        $lastParamTag = end($paramTags);
        if (! $lastParamTag instanceof Param) {
            return;
        }

        if (
            ! $lastParamTag->isVariadic()
            || array_key_exists($lastParamTag->getVariableName(), $methodDescriptor->getArguments()->getAll())
        ) {
            return;
        }

        $types = $lastParamTag->getType();

        $argument = new Argument(
            $lastParamTag->getVariableName(),
            $types,
            null,
            $lastParamTag->isReference(),
            $lastParamTag->isVariadic(),
        );

        $argumentDescriptor = $this->argumentAssembler->create(
            $argument,
            $methodDescriptor->getTags()->fetch('param', new Collection())->filter(ParamDescriptor::class),
        );

        $methodDescriptor->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
    }
}
