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
use phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\AuthorTag;
use phpDocumentor\Reflection\DocBlock\Tags\Author;

/**
 * Constructs a new descriptor from the Reflector for an `@author` tag.
 *
 * This object will read the reflected information for the `@author` tag and create a {@see AuthorDescriptor} object
 * that can be used in the rest of the application and templates.
 */
class AuthorAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param Author $data
     *
     * @return AuthorDescriptor
     */
    public function create($data)
    {
        $descriptor = new AuthorDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());

        return $descriptor;
    }
}
