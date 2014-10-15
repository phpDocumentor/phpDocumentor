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
use phpDocumentor\Descriptor\Tag\LinkDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\LinkTag;

/**
 * Constructs a new descriptor from the Reflector for an `@link` tag.
 *
 * This object will read the reflected information for the `@link` tag and create a {@see LinkDescriptor} object that
 * can be used in the rest of the application and templates.
 */
class LinkAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param LinkTag $data
     *
     * @return LinkDescriptor
     */
    public function create($data)
    {
        $descriptor = new LinkDescriptor($data->getName());
        $descriptor->setLink($data->getLink());
        $descriptor->setDescription($data->getDescription());

        return $descriptor;
    }
}
