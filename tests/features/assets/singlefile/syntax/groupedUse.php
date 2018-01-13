<?php

namespace phpDocumentor;

use phpDocumentor\{ClassA, ClassB, ClassC as C};

class A
{
    /**
     * @param \phpDocumentor\ClassA $a
     * @param \phpDocumentor\ClassB $b
     * @param ClassC $c
     */
    public function someMethodWithDocblock(ClassA $a, ClassB $b, C $c)
    {
    }

    public function someMethodWithoutDocblock(ClassA $a, ClassB $b, C $c)
    {
    }
}
