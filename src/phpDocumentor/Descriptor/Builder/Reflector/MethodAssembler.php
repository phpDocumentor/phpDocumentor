<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Method;

/**
 * Assembles a MethodDescriptor from a MethodReflector.
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
     *
     * @return MethodDescriptor
     */
    public function create($data)
    {
        $methodDescriptor = new MethodDescriptor();
        $methodDescriptor->setNamespace(
            substr(
                (string) $data->getFqsen(),
                0,
                -strlen($data->getName()) - 4
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
    protected function mapReflectorToDescriptor(Method $reflector, MethodDescriptor $descriptor): void
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
        $params = $descriptor->getTags()->get('param', []);

        if (!$this->argumentAssembler->getBuilder()) {
            $this->argumentAssembler->setBuilder($this->builder);
        }

        $argumentDescriptor = $this->argumentAssembler->create($argument, $params);

        $descriptor->addArgument($argumentDescriptor->getName(), $argumentDescriptor);
    }

    /**
     * Checks if there is a variadic argument in the `@param` tags and adds it to the list of Arguments in
     * the Descriptor unless there is already one present.
     */
    protected function addVariadicArgument(Method $data, MethodDescriptor $methodDescriptor): void
    {
        if (!$data->getDocBlock()) {
            return;
        }

        $paramTags = $data->getDocBlock()->getTagsByName('param');

        /** @var Param|bool $lastParamTag */
        $lastParamTag = end($paramTags);
        if ($lastParamTag === false) {
            return;
        }

        if ($lastParamTag->isVariadic()
            && array_key_exists($lastParamTag->getVariableName(), $methodDescriptor->getArguments()->getAll())
        ) {
            $types = $lastParamTag->getType();

            $argument = new ArgumentDescriptor();
            $argument->setName($lastParamTag->getVariableName());
            $argument->setType($types);
            $argument->setDescription($lastParamTag->getDescription());
            $argument->setLine($methodDescriptor->getLine());
            $argument->setVariadic(true);

            $methodDescriptor->getArguments()->set($argument->getName(), $argument);
        }
    }
}
