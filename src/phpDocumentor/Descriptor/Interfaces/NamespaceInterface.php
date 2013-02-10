<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

interface NamespaceInterface extends BaseInterface
{
    /**
     * @return Collection
     */
    public function getClasses();

    /**
     * @return Collection
     */
    public function getConstants();

    /**
     * @return Collection
     */
    public function getFunctions();

    /**
     * @return Collection
     */
    public function getInterfaces();

    /**
     * @return Collection
     */
    public function getNamespaces();

    /**
     * @return Collection
     */
    public function getTraits();
}
