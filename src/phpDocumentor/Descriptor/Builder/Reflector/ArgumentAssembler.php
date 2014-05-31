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
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Reflection\FunctionReflector\ArgumentReflector;

/**
 * Assembles an ArgumentDescriptor using an ArgumentReflector and ParamDescriptors.
 */
class ArgumentAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param ArgumentReflector $data
     * @param ParamDescriptor[] $params
     *
     * @return ArgumentDescriptor
     */
    public function create($data, $params = array())
    {
        $argumentDescriptor = new ArgumentDescriptor();
        $argumentDescriptor->setName($data->getName());
        $argumentDescriptor->setTypes(
            $this->builder->buildDescriptor(
                $data->getType() ? new Collection(array($data->getType())) : new Collection()
            )
        );

        foreach ($params as $paramDescriptor) {
            $this->overwriteTypeAndDescriptionFromParamTag($data, $paramDescriptor, $argumentDescriptor);
        }

        $argumentDescriptor->setDefault($data->getDefault());
        $argumentDescriptor->setByReference($data->isByRef());

        return $argumentDescriptor;
    }

    /**
     * Overwrites the type and description in the Argument Descriptor with that from the tag if the names match.
     *
     * @param ArgumentReflector  $argument
     * @param ParamDescriptor    $paramDescriptor
     * @param ArgumentDescriptor $argumentDescriptor
     *
     * @return void
     */
    protected function overwriteTypeAndDescriptionFromParamTag(
        ArgumentReflector  $argument,
        ParamDescriptor    $paramDescriptor,
        ArgumentDescriptor $argumentDescriptor
    ) {
        if ($paramDescriptor->getVariableName() != $argument->getName()) {
            return;
        }

        $argumentDescriptor->setDescription($paramDescriptor->getDescription());
        $argumentDescriptor->setTypes(
            $paramDescriptor->getTypes() ?: $this->builder->buildDescriptor(
                new Collection(array($argument->getType() ?: 'mixed'))
            )
        );
    }
}
