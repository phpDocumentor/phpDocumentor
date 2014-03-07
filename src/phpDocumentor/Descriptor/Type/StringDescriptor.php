<?php

namespace phpDocumentor\Descriptor\Type;

use phpDocumentor\Descriptor\Interfaces\TypeInterface;

class StringDescriptor implements TypeInterface
{
    public function getName()
    {
        return 'string';
    }
}