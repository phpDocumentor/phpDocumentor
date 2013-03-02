<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

interface FileInterface extends BaseInterface, ReferencingInterface
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
    public function getConstants();

    /**
     * @return Collection
     */
    public function getFunctions();

    /**
     * @return Collection
     */
    public function getClasses();

    /**
     * @return Collection
     */
    public function getInterfaces();

    /**
     * @return Collection
     */
    public function getTraits();

    /**
     * @return Collection
     */
    public function getErrors();
}
