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

use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\SeeTag;

class SeeDescriptor extends TagDescriptor
{
    protected $reference = '';

    /**
     * Reads reference from SeeTag
     *
     * @param SeeTag $reflectionTag
     */
    public function __construct(SeeTag $reflectionTag)
    {
        parent::__construct($reflectionTag);

        $this->reference = $reflectionTag->getReference();
    }

    /**
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }
}
