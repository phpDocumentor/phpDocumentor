<?php

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag;

class TagFactory
{
    protected $tags = array(
        'author' => 'phpDocumentor\Descriptor\Tag\AuthorDescriptor',
        'return' => 'phpDocumentor\Descriptor\Tag\ReturnDescriptor',
        'see'    => 'phpDocumentor\Descriptor\Tag\SeeDescriptor',
        'link'   => 'phpDocumentor\Descriptor\Tag\LinkDescriptor',
        'uses'   => 'phpDocumentor\Descriptor\Tag\UsesDescriptor',
        'param'  => 'phpDocumentor\Descriptor\Tag\ParamDescriptor',
        'throws' => 'phpDocumentor\Descriptor\Tag\ThrowsDescriptor',
        'throw'  => 'phpDocumentor\Descriptor\Tag\ThrowsDescriptor',
    );

    public function create(Tag $reflectorTag)
    {
        $tagName = $reflectorTag->getName();
        $tagClassName = isset($this->tags[$tagName]) ? $this->tags[$tagName] : null;

        $descriptor = ($tagClassName === null)
            ? new TagDescriptor($reflectorTag)
            : new $tagClassName($reflectorTag);

        return $descriptor;
    }
}
