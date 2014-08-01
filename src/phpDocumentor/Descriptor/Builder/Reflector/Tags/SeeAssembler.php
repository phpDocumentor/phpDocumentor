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

use phpDocumentor\Compiler\Linker\Linker;
use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Tag\SeeDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\SeeTag;
use phpDocumentor\Reflection\DocBlock\Type\Collection;

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
     * @param SeeTag $data
     *
     * @return SeeDescriptor
     */
    public function create($data)
    {
        $descriptor = new SeeDescriptor($data->getName());
        $descriptor->setDescription($data->getDescription());

        $reference = $data->getReference();

        if (substr($reference, 0, 7) !== 'http://'
            && substr($reference, 0, 8) !== 'https://'
            && $reference !== 'self'
            && $reference !== '$this'
        ) {
            // TODO: move this to the ReflectionDocBlock component
            // Expand FQCN part of the FQSEN
            $referenceParts = explode('::', $reference);
            if (count($referenceParts) > 1 && $reference[0] != '\\') {
                $type = current($referenceParts);
                $type = new Collection(
                    array($type),
                    $data->getDocBlock() ? $data->getDocBlock()->getContext() : null
                );
                $referenceParts[0] = $type;
            } elseif (isset($reference[0]) && $reference[0] != '\\') {
                array_unshift($referenceParts, Linker::CONTEXT_MARKER);
            }

            $reference = implode('::', $referenceParts);
        }

        $descriptor->setReference($reference);

        return $descriptor;
    }
}
