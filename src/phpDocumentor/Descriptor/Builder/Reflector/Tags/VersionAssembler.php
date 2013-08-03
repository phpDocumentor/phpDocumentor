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
use phpDocumentor\Descriptor\Tag\VersionDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\VersionTag;

class VersionAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param VersionTag $data
     *
     * @return VersionDescriptor
     */
    public function create($data)
    {
        $descriptor = new VersionDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setVersion($data->getVersion());

        return $descriptor;
    }
}
