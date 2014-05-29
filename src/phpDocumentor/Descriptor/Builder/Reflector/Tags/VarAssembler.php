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

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Tag\VarDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\VarTag;
use phpDocumentor\Reflection\DocBlock\Type\Collection;

class VarAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param VarTag $data
     *
     * @return VarDescriptor
     */
    public function create($data)
    {
        $descriptor = new VarDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setVariableName($data->getVariableName());

        $types = $this->builder->buildDescriptor(
            new Collection($data->getVariableName() == '$this' ? array('$this') : $data->getTypes())
        );
        $descriptor->setTypes($types);

        return $descriptor;
    }
}
