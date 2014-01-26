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
use phpDocumentor\Descriptor\Tag\PropertyDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\PropertyTag;

class PropertyAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param PropertyTag $data
     *
     * @return PropertyDescriptor
     */
    public function create($data)
    {
        $descriptor = new PropertyDescriptor($data->getName());
        $descriptor->setVariableName($data->getVariableName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setTypes($data->getTypes());

        return $descriptor;
    }
}
