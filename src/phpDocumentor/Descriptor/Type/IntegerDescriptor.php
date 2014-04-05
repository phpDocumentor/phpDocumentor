<?php

namespace phpDocumentor\Descriptor\Type;

use phpDocumentor\Descriptor\Interfaces\TypeInterface;

class IntegerDescriptor implements TypeInterface
{
    public function getName()
    {
        return 'integer';
    }

    public function __toString()
    {
        return $this->getName();
    }
}
