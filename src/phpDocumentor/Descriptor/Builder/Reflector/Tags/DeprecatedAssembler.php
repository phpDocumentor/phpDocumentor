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

use phpDocumentor\Descriptor\Tag\DeprecatedDescriptor;
use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Reflection\DocBlock\Tag\DeprecatedTag;

/**
 * Constructs a new descriptor from the Reflector for an `@deprecated` tag.
 *
 * This object will read the reflected information for the `@deprecated` tag and create a {@see DeprecatedDescriptor}
 * object that can be used in the rest of the application and templates.
 */
class DeprecatedAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param DeprecatedTag $data
     *
     * @return DeprecatedDescriptor
     */
    public function create($data)
    {
        $descriptor = new DeprecatedDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setVersion($data->getVersion());

        return $descriptor;
    }
}
