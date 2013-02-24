<?php
namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;

class ParamDescriptor extends TagDescriptor
{
    protected $variableName = '';
    protected $types;

    public function __construct(ParamTag $reflectionTag)
    {
        parent::__construct($reflectionTag);

        $this->variableName = $reflectionTag->getVariableName();
        $this->types = $reflectionTag->getTypes();
    }

    public function getVariableName()
    {
        return $this->variableName;
    }

    public function getTypes()
    {
        return $this->types;
    }

}