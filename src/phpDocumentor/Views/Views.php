<?php

namespace phpDocumentor\Views;

use Webmozart\Assert\Assert;

class Views extends \ArrayObject
{
    public function offsetSet($index, $newval)
    {
        Assert::stringNotEmpty($index);
        Assert::isInstanceOf($newval, View::class);

        parent::offsetSet($index, $newval);
    }
}
