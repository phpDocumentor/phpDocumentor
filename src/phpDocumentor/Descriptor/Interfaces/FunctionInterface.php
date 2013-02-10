<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

interface FunctionInterface extends BaseInterface
{
    /**
     * @return Collection
     */
    public function getArguments();
}
