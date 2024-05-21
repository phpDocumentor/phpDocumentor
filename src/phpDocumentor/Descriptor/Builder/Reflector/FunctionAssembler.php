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
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\Interfaces\ArgumentInterface;
use phpDocumentor\Descriptor\Interfaces\FunctionInterface;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Function_;

use function strlen;
use function substr;
use function trim;

/**
 * Assembles a FunctionDescriptor from a FunctionReflector.
 *
 * @extends AssemblerAbstract<FunctionInterface, Function_>
 */
class FunctionAssembler extends AssemblerAbstract
{
    /**
     * Initializes this assembler and its dependencies.
     */
    public function __construct(private readonly ArgumentAssembler $argumentAssembler, AssemblerReducer ...$reducers)
    {
        parent::__construct(...$reducers);
    }

    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Function_ $data
     */
    public function buildDescriptor(object $data): FunctionInterface
    {
        $functionDescriptor = new FunctionDescriptor();

        $this->mapReflectorPropertiesOntoDescriptor($data, $functionDescriptor);
        $this->assembleDocBlock($data->getDocBlock(), $functionDescriptor);
        $this->addArgumentsToFunctionDescriptor($data->getArguments(), $functionDescriptor);

        return $functionDescriptor;
    }

    /**
     * Maps the properties of the Function reflector onto the Descriptor.
     */
    protected function mapReflectorPropertiesOntoDescriptor(Function_ $reflector, FunctionDescriptor $descriptor): void
    {
        $packages = Collection::fromClassString(TagDescriptor::class);
        $package  = $this->extractPackageFromDocBlock($reflector->getDocBlock());
        //TODO: this looks like a potential bug. Have to investigate this!
        if ($package) {
            $tag = new TagDescriptor('package');
            $tag->setDescription(new DescriptionDescriptor(new Description($package), []));
            $packages->add($tag);
        }

        $descriptor->getTags()->set('package', $packages);

        $descriptor->setFullyQualifiedStructuralElementName($reflector->getFqsen());
        $descriptor->setName($reflector->getName());
        $descriptor->setStartLocation($reflector->getLocation());
        $descriptor->setEndLocation($reflector->getEndLocation());
        $descriptor->setNamespace('\\' . trim(substr(
            (string) $reflector->getFqsen(),
            0,
            -strlen($reflector->getName()) - 2,
        ), '\\'));
        $descriptor->setReturnType($reflector->getReturnType());
        $descriptor->setHasReturnByReference($reflector->getHasReturnByReference());
    }

    /**
     * Converts each argument reflector to an argument descriptor and adds it to the function descriptor.
     *
     * @param Argument[] $arguments
     */
    protected function addArgumentsToFunctionDescriptor(array $arguments, FunctionDescriptor $functionDescriptor): void
    {
        foreach ($arguments as $argument) {
            $descriptor = $this->createArgumentDescriptor($functionDescriptor, $argument);
            $descriptor->setStartLocation($functionDescriptor->getStartLocation());
            $descriptor->setEndLocation($functionDescriptor->getEndLocation());

            $this->addArgumentDescriptorToFunction(
                $functionDescriptor,
                $descriptor,
            );
        }
    }

    /**
     * Adds the given argument to the function.
     */
    protected function addArgumentDescriptorToFunction(
        FunctionInterface $functionDescriptor,
        ArgumentInterface $argumentDescriptor,
    ): void {
        $functionDescriptor->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
    }

    /**
     * Creates a new ArgumentDescriptor from the given Reflector and Param.
     */
    protected function createArgumentDescriptor(
        FunctionDescriptor $functionDescriptor,
        Argument $argument,
    ): ArgumentInterface {
        /** @var Collection<ParamDescriptor> $params */
        $params = $functionDescriptor->getTags()->fetch('param', new Collection())->filter(ParamDescriptor::class);

        if (! $this->argumentAssembler->getBuilder()) {
            $this->argumentAssembler->setBuilder($this->builder);
        }

        return $this->argumentAssembler->create($argument, $params);
    }
}
