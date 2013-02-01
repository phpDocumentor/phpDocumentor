<?php
namespace phpDocumentor\Descriptor;

class ClassDescriptor extends DescriptorAbstract
{
    protected $extends;
    protected $implements = array();

    protected $abstract = false;
    protected $final = false;

    protected $constants = array();
    protected $properties = array();
    protected $methods = array();
}