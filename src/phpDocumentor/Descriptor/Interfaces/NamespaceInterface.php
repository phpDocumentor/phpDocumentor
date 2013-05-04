<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

interface NamespaceInterface extends BaseInterface, ContainerInterface
{
    /**
     * @return static
     */
    public function getParent();

    /**
     * @return Collection
     */
    public function getChildren();
}
