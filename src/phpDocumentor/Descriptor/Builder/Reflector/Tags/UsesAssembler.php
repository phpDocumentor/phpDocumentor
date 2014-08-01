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
use phpDocumentor\Descriptor\Tag\UsesDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\UsesTag;

class UsesAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param UsesTag $data
     *
     * @return UsesDescriptor
     */
    public function create($data)
    {
        $descriptor = new UsesDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setReference($data->getReference());

        return $descriptor;
    }
}
