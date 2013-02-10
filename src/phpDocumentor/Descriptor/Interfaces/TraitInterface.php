<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

interface TraitInterface extends BaseInterface
{
    /**
     * @return Collection
     */
    public function getMethods();

    /**
     * @return Collection
     */
    public function getProperties();
}
