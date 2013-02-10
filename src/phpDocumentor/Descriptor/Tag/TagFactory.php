<?php

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\TagDescriptor;

class TagFactory
{
    protected $tags = array(
        'author' => 'phpDocumentor\Descriptor\Tag\AuthorDescriptor',
        'return' => 'phpDocumentor\Descriptor\Tag\ReturnDescriptor',
        'See'    => 'phpDocumentor\Descriptor\Tag\SeeDescriptor',
        'Uses'   => 'phpDocumentor\Descriptor\Tag\UsesDescriptor',
    );

    public function create($tagName, $tagDescription)
    {
        $tagClassName = isset($this->tags[$tagName]) ? $this->tags[$tagName] : null;
        if ($tagClassName === null){
            $descriptor = new TagDescriptor($tagDescription);
            $descriptor->setName($tagName);
            return $descriptor;
        }

        return new $tagClassName($tagDescription);
    }
}