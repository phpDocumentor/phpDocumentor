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
use phpDocumentor\Reflection\DocBlock\Tag\LinkTag;

class LinkDescriptor extends TagDescriptor
{
    protected $link;

    public function __construct(LinkTag $reflectionTag)
    {
        parent::__construct($reflectionTag);

        $this->link = $reflectionTag->getLink();
    }
}
