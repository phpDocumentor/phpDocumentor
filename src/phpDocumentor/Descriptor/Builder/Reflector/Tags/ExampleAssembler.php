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
use phpDocumentor\Descriptor\Tag\ExampleDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\ExampleTag;
use phpDocumentor\Configuration\Files;

class ExampleAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param ExampleTag $data
     *
     * @return ExampleDescriptor
     */
    public function create($data)
    {
        $descriptor = new ExampleDescriptor($data->getName());
        $descriptor->setFilePath($data->getFilePath());
        $descriptor->setStartingLine($data->getStartingLine());
        $descriptor->setLineCount($data->getLineCount());
        $descriptor->setDescription($data->getDescription());
        $descriptor->setExample($data->getContent());

        return $descriptor;
    }
}