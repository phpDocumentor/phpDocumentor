<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

interface ProjectInterface
{
    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return Collection
     */
    public function getFiles();

    /**
     * @return Collection
     */
    public function getIndexes();

    /**
     * @return NamespaceInterface
     */
    public function getNamespace();
}
