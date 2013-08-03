<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

interface NamespaceInterface extends BaseInterface, ContainerInterface, ChildInterface
{
    /**
     * @return Collection
     */
    public function getChildren();
}
