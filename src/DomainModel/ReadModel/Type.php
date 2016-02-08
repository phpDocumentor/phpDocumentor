<?php

namespace phpDocumentor\DomainModel\ReadModel;

use Webmozart\Assert\Assert;

class Type
{
    /** @var string */
    private $type;

    public function __construct($type)
    {
        Assert::stringNotEmpty($type);

        $this->type = $type;
    }

    public function __toString()
    {
        return $this->type;
    }
}
