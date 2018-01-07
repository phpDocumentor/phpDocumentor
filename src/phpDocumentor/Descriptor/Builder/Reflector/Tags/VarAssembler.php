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

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Tag\VarDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

/**
 * Constructs a new descriptor from the Reflector for an `@var` tag.
 *
 * This object will read the reflected information for the `@var` tag and create a {@see VarDescriptor} object that
 * can be used in the rest of the application and templates.
 */
class VarAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param Var_ $data
     *
     * @return VarDescriptor
     */
    public function create($data)
    {
        $descriptor = new VarDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setVariableName($data->getVariableName());

        //$types = $this->builder->buildDescriptor();
        $descriptor->setTypes($data->getType());

        return $descriptor;
    }
}
