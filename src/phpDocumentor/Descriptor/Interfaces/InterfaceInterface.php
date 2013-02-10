<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

interface InterfaceInterface extends BaseInterface
{
    /**
     * @return Collection
     */
    public function getParentInterfaces();

    /**
     * @return Collection
     */
    public function getMethods();
}
