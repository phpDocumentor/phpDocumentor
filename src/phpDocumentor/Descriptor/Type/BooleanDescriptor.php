<?php

namespace phpDocumentor\Descriptor\Type;

use phpDocumentor\Descriptor\Interfaces\TypeInterface;

class BooleanDescriptor implements TypeInterface
{
    public function getName()
    {
        return 'boolean';
    }

    public function __toString()
    {
        return $this->getName();
    }
}
