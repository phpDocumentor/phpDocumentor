<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

interface PackageInterface extends BaseInterface, ContainerInterface
{
    /**
     * @return PackageInterface
     */
    public function getParent();

    /**
     * @return Collection
     */
    public function getChildren();
}
