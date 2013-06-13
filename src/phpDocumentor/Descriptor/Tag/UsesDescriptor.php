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

use phpDocumentor\Reflection\DocBlock\Tag\UsesTag;

class UsesDescriptor extends BaseTypes\TypedAbstract
{
    /**
     * @param UsesTag $reflectionTag
     */
    public function __construct($reflectionTag)
    {
        $this->name        = $reflectionTag->getName();
        $this->description = $reflectionTag->getDescription();
        $this->types       = $reflectionTag->getReference();
    }
}
