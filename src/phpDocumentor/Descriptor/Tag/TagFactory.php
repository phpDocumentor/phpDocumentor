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
        'uses'   => 'phpDocumentor\Descriptor\Tag\UsesDescriptor',
        'param'  => 'phpDocumentor\Descriptor\Tag\ParamDescriptor',
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