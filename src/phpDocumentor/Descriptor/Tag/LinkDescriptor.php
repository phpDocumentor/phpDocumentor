<?php
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
