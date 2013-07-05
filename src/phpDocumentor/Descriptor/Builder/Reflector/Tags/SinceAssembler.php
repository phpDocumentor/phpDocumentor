<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Tag\SinceDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\SinceTag;

class SinceAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param SinceTag $data
     *
     * @return SinceDescriptor
     */
    public function create($data)
    {
        $descriptor = new SinceDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setVersion($data->getVersion());

        return $descriptor;
    }
}
