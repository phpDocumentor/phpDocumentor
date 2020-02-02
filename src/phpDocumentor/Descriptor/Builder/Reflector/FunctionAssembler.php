<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Function_;
use function strlen;
use function substr;
use function trim;

/**
 * Assembles a FunctionDescriptor from a FunctionReflector.
 */
class FunctionAssembler extends AssemblerAbstract
{
    /** @var ArgumentAssembler */
    protected $argumentAssembler;

    /**
     * Initializes this assembler and its dependencies.
     */
    public function __construct(ArgumentAssembler $argumentAssembler)
    {
        $this->argumentAssembler = $argumentAssembler;
    }

    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Function_ $data
     */
    public function create($data) : FunctionDescriptor
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
    protected function mapReflectorPropertiesOntoDescriptor(Function_ $reflector, FunctionDescriptor $descriptor) : void
    {
        /** @var Collection<TagDescriptor> $packages */
        $packages = new Collection();
        $package  = $this->extractPackageFromDocBlock($reflector->getDocBlock());
        //TODO: this looks like a potential bug. Have to investigate this!
        if ($package) {
            $tag = new TagDescriptor('package');
            $tag->setDescription($package);
            $packages->add($tag);
        }

        $descriptor->getTags()->set('package', $packages);

        $descriptor->setFullyQualifiedStructuralElementName($reflector->getFqsen());
        $descriptor->setName($reflector->getName());
        $descriptor->setLine($reflector->getLocation()->getLineNumber());
        $descriptor->setNamespace('\\' . trim(substr(
            (string) $reflector->getFqsen(),
            0,
            -strlen($reflector->getName()) - 2
        ), '\\'));
        $descriptor->setReturnType($reflector->getReturnType());
    }

    /**
     * Converts each argument reflector to an argument descriptor and adds it to the function descriptor.
     *
     * @param Argument[] $arguments
     */
    protected function addArgumentsToFunctionDescriptor(array $arguments, FunctionDescriptor $functionDescriptor) : void
    {
        foreach ($arguments as $argument) {
            $this->addArgumentDescriptorToFunction(
                $functionDescriptor,
                $this->createArgumentDescriptor($functionDescriptor, $argument)
            );
        }
    }

    /**
     * Adds the given argument to the function.
     */
    protected function addArgumentDescriptorToFunction(
        FunctionDescriptor $functionDescriptor,
        ArgumentDescriptor $argumentDescriptor
    ) : void {
        $functionDescriptor->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
    }

    /**
     * Creates a new ArgumentDescriptor from the given Reflector and Param.
     */
    protected function createArgumentDescriptor(
        FunctionDescriptor $functionDescriptor,
        Argument $argument
    ) : ArgumentDescriptor {
        /** @var Collection<ParamDescriptor> $params */
        $params = $functionDescriptor->getTags()->get('param', new Collection())->filter(ParamDescriptor::class);

        if (!$this->argumentAssembler->getBuilder()) {
            $this->argumentAssembler->setBuilder($this->builder);
        }

        return $this->argumentAssembler->create($argument, $params);
    }
}
