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
use phpDocumentor\Reflection\DocBlock\Tag\MethodTag;

class MethodDescriptor extends TagDescriptor
{
    protected $methodName = '';

    public function __construct(MethodTag $reflectionTag)
    {
        parent::__construct($reflectionTag);

        $this->methodName = $reflectionTag->getMethodName();

        // TODO: add response and arguments
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }
}
