<?php

namespace phpDocumentor\DomainModel\ReadModel;

use Webmozart\Assert\Assert;

class Collection extends \ArrayObject
{
    public function offsetSet($index, $newval)
    {
        Assert::stringNotEmpty($index);
        Assert::isInstanceOf($newval, ReadModel::class);

        parent::offsetSet($index, $newval);
    }
}
