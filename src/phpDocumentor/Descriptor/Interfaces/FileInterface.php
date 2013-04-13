<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

interface FileInterface extends BaseInterface, ContainerInterface
{
    public function getHash();

    public function setSource($source);

    public function getSource();

    /**
     * @return Collection
     */
    public function getNamespaceAliases();

    /**
     * @return Collection
     */
    public function getIncludes();

    /**
     * @return Collection
     */
    public function getErrors();
}
