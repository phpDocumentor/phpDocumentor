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
use phpDocumentor\Descriptor\Tag\MethodDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\MethodTag;

class MethodAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param MethodTag $data
     *
     * @return MethodDescriptor
     */
    public function create($data)
    {
        $descriptor = new MethodDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setMethodName($data->getMethodName());

        // TODO: add response and arguments.

        return $descriptor;
    }
}
