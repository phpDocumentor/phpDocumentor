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
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Param;

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
     * @param Param $data
     *
     * @return ParamDescriptor
     */
    public function create($data)
    {
        $descriptor = new ParamDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setVariableName($data->getVariableName());
        $descriptor->setTypes($data->getType());

        return $descriptor;
    }
}
