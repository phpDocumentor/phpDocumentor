<?php

namespace phpDocumentor\Views;

use Webmozart\Assert\Assert;

class ViewType
{
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
