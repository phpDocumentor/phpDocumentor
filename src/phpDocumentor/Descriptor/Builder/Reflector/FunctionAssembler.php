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
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\FunctionReflector;

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
     * @param FunctionReflector $data
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
     * @param FunctionReflector  $reflector
     * @param FunctionDescriptor $descriptor
     *
     * @return void
     */
    protected function mapReflectorPropertiesOntoDescriptor($reflector, $descriptor)
    {
        $packages = new Collection();
        $package = $this->extractPackageFromDocBlock($reflector->getDocBlock());
        if ($package) {
            $tag = new TagDescriptor('package');
            $tag->setDescription($package);
            $packages->add($tag);
        }
        $descriptor->getTags()->set('package', $packages);

        $descriptor->setFullyQualifiedStructuralElementName($reflector->getName() . '()');
        $descriptor->setName($reflector->getShortName());
        $descriptor->setLine($reflector->getLinenumber());
        $descriptor->setNamespace($this->getFullyQualifiedNamespaceName($reflector));
    }

    /**
     * Converts each argument reflector to an argument descriptor and adds it to the function descriptor.
     *
     * @param FunctionReflector\ArgumentReflector[] $arguments
     * @param FunctionDescriptor                    $functionDescriptor
     *
     * @return void
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
     *
     * @return void
     */
    protected function addArgumentDescriptorToFunction($functionDescriptor, $argumentDescriptor)
    {
        $functionDescriptor->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
    }

    /**
     * Creates a new ArgumentDescriptor from the given Reflector and Param.
     *
     * @param FunctionDescriptor                  $functionDescriptor
     * @param FunctionReflector\ArgumentReflector $argument
     *
     * @return ArgumentDescriptor
     */
    protected function createArgumentDescriptor($functionDescriptor, $argument)
    {
        $params = $functionDescriptor->getTags()->get('param', array());

        if (!$this->argumentAssembler->getBuilder()) {
            $this->argumentAssembler->setBuilder($this->builder);
        }

        return $this->argumentAssembler->create($argument, $params);
    }

    /**
     * Retrieves the Fully Qualified Namespace Name from the FunctionReflector.
     *
     * Reflection library formulates namespace as global but this is not wanted for phpDocumentor itself.
     *
     * @param FunctionReflector $reflector
     *
     * @return string
     */
    protected function getFullyQualifiedNamespaceName($reflector)
    {
        return '\\' . (strtolower($reflector->getNamespace()) == 'global' ? '' : $reflector->getNamespace());
    }
}
