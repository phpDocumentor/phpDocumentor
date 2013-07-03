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

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\SeeTag;
use phpDocumentor\Reflection\DocBlock\Type\Collection;

class SeeDescriptor extends TagDescriptor
{
    /** @var DescriptorAbstract|string $reference */
    protected $reference = '';

    /**
     * Reads reference from SeeTag
     *
     * @param SeeTag $reflectionTag
     */
    public function __construct(SeeTag $reflectionTag)
    {
        parent::__construct($reflectionTag);

        // TODO: move this to the ReflectionDocBlock component
        $referenceParts = explode('::', $reflectionTag->getReference());
        $type = current($referenceParts);
        $type = new Collection(
            array($type),
            $reflectionTag->getDocBlock() ? $reflectionTag->getDocBlock()->getContext() : null
        );
        $referenceParts[0] = $type;

        $this->reference = implode('::', $referenceParts);
    }

    /**
     * @param DescriptorAbstract|string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return DescriptorAbstract|string
     */
    public function getReference()
    {
        return $this->reference;
    }
}
