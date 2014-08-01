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
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;
use phpDocumentor\Reflection\DocBlock\Type\Collection;

/**
 * Constructs a new descriptor from the Reflector for an `@param` tag.
 *
 * This object will read the reflected information for the `@param` tag and create a {@see ParamDescriptor} object that
 * can be used in the rest of the application and templates.
 */
class ParamAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param ParamTag $data
     *
     * @return ParamDescriptor
     */
    public function create($data)
    {
        $descriptor = new ParamDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setVariableName($data->getVariableName());

        /** @var Collection $types */
        $types = $this->builder->buildDescriptor(new Collection($data->getTypes()));
        $descriptor->setTypes($types);

        return $descriptor;
    }
}
