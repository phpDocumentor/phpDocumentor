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
use phpDocumentor\Reflection\DocBlock\Tag\UsesTag;

class UsesDescriptor extends TagDescriptor
{
    /** @var string the FQCN where the uses tag refers to */
    protected $reference = '';

    /**
     * @param UsesTag $reflectionTag
     */
    public function __construct($reflectionTag)
    {
        parent::__construct($reflectionTag);

        $this->reference = $reflectionTag->getReference();
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }
}
