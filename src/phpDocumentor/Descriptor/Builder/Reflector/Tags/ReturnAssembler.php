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
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;

/**
 * Constructs a new descriptor from the Reflector for an `@return` tag.
 *
 * This object will read the reflected information for the `@return` tag and create a {@see ReturnDescriptor} object
 * that can be used in the rest of the application and templates.
 */
class ReturnAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param Return_ $data
     *
     * @return ReturnDescriptor
     */
    public function create($data)
    {
        $descriptor = new ReturnDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setTypes($data->getType());

        return $descriptor;
    }
}
