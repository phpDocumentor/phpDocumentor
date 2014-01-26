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
use phpDocumentor\Descriptor\Tag\SeeDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\SeeTag;
use phpDocumentor\Reflection\DocBlock\Type\Collection;

class SeeAssembler extends AssemblerAbstract
{
    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param SeeTag $data
     *
     * @return SeeDescriptor
     */
    public function create($data)
    {
        $descriptor = new SeeDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());

        // TODO: move this to the ReflectionDocBlock component
        // Expand FQCN part of the FQSEN
        $referenceParts = explode('::', $data->getReference());
        $type = current($referenceParts);
        $type = new Collection(
            array($type),
            $data->getDocBlock() ? $data->getDocBlock()->getContext() : null
        );
        $referenceParts[0] = $type;

        $descriptor->setReference(implode('::', $referenceParts));

        return $descriptor;
    }
}
