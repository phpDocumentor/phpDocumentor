<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

interface InterfaceInterface extends BaseInterface, ReferencingInterface
{
    /**
     * @return Collection
     */
    public function getParentInterfaces();

    /**
     * @return Collection
     */
    public function getMethods();

    /**
     * @return Collection
     */
    public function getConstants();
}
