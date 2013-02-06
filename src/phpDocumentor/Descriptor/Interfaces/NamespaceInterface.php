<?php
namespace phpDocumentor\Descriptor\Interfaces;

interface NamespaceInterface extends BaseInterface
{
    /**
     * @return \ArrayObject
     */
    public function getClasses();

    /**
     * @return \ArrayObject
     */
    public function getConstants();

    /**
     * @return \ArrayObject
     */
    public function getFunctions();

    /**
     * @return \ArrayObject
     */
    public function getInterfaces();

    /**
     * @return \ArrayObject
     */
    public function getNamespaces();

    /**
     * @return \ArrayObject
     */
    public function getTraits();
}
