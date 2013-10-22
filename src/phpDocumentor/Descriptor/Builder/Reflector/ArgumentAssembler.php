<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ArgumentDescriptor;
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
        $argumentDescriptor->setTypes($data->getType() ? array($data->getType()) : array());

        /** @var ParamDescriptor $tag */
        foreach ($params as $tag) {
            if ($tag->getVariableName() == $data->getName()) {
                $argumentDescriptor->setDescription($tag->getDescription());

                $types = $tag->getTypes() ?: array($data->getType() ?: 'mixed');
                $argumentDescriptor->setTypes($types);
            }
        }

        $argumentDescriptor->setDefault($data->getDefault());
        $argumentDescriptor->setByReference($data->isByRef());

        return $argumentDescriptor;
    }
}
