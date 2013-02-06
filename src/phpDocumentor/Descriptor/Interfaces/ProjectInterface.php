<?php
namespace phpDocumentor\Descriptor\Interfaces;

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
     * @return \ArrayObject
     */
    public function getFiles();

    /**
     * @return \ArrayObject
     */
    public function getIndexes();

    /**
     * @return NamespaceInterface
     */
    public function getNamespace();
}
