<?php
namespace phpDocumentor\Descriptor;

class MethodDescriptor extends DescriptorAbstract
{
    protected $abstract = false;
    protected $final = false;
    protected $static = false;
    protected $visibility = 'public';

    protected $arguments = array();
}
