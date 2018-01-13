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
use phpDocumentor\Descriptor\Tag\SeeDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\See;

/**
 * Constructs a new Descriptor from a Reflector object for the `@see` tag.
 *
 * This class will gather the properties that were parsed by the Reflection mechanism for, specifically, an `@see` tag
 * and use that to create a SeeDescriptor that describes all properties that an `@see` tag may have.
 */
class SeeAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param See $data
     *
     * @return SeeDescriptor
     */
    public function create($data)
    {
        $descriptor = new SeeDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());

        $reference = $data->getReference();
        $descriptor->setReference($reference);

        return $descriptor;
    }
}
