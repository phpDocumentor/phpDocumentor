<?php

namespace phpDocumentor\Descriptor\Type;

use phpDocumentor\Descriptor\Interfaces\TypeInterface;

class FloatDescriptor implements TypeInterface
{
    public function getName()
    {
        return 'float';
    }

    public function __toString()
    {
        return $this->getName();
    }
}
