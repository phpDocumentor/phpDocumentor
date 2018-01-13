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

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\FunctionReflector;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Function_;

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
     *
     * @return FunctionDescriptor
     */
    public function create($data)
    {
        $functionDescriptor = new FunctionDescriptor();

        $this->mapReflectorPropertiesOntoDescriptor($data, $functionDescriptor);
        $this->assembleDocBlock($data->getDocBlock(), $functionDescriptor);
        $this->addArgumentsToFunctionDescriptor($data->getArguments(), $functionDescriptor);

        return $functionDescriptor;
    }

    /**
     * Maps the properties of the Function reflector onto the Descriptor.
     *
     * @param Function_  $reflector
     * @param FunctionDescriptor $descriptor
     */
    protected function mapReflectorPropertiesOntoDescriptor($reflector, $descriptor)
    {
        $packages = new Collection();
        $package = $this->extractPackageFromDocBlock($reflector->getDocBlock());
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
        $descriptor->setNamespace(substr($reflector->getFqsen(), 0, -strlen($reflector->getName())));
        $descriptor->setReturnType($reflector->getReturnType());
    }

    /**
     * Converts each argument reflector to an argument descriptor and adds it to the function descriptor.
     *
     * @param Argument[] $arguments
     * @param FunctionDescriptor                    $functionDescriptor
     */
    protected function addArgumentsToFunctionDescriptor(array $arguments, $functionDescriptor)
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
     *
     * @param FunctionDescriptor $functionDescriptor
     * @param ArgumentDescriptor $argumentDescriptor
     */
    protected function addArgumentDescriptorToFunction($functionDescriptor, $argumentDescriptor)
    {
        $functionDescriptor->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
    }

    /**
     * Creates a new ArgumentDescriptor from the given Reflector and Param.
     *
     * @return ArgumentDescriptor
     */
    protected function createArgumentDescriptor(FunctionDescriptor $functionDescriptor, Argument $argument)
    {
        $params = $functionDescriptor->getTags()->get('param', []);

        if (!$this->argumentAssembler->getBuilder()) {
            $this->argumentAssembler->setBuilder($this->builder);
        }

        return $this->argumentAssembler->create($argument, $params);
    }
}
