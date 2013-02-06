<?php
namespace phpDocumentor\Descriptor\Interfaces;

interface FileInterface extends BaseInterface
{
    public function getHash();

    public function setSource($source);

    public function getSource();

    /**
     * @return \ArrayObject
     */
    public function getNamespaceAliases();

    /**
     * @return \ArrayObject
     */
    public function getIncludes();

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
    public function getClasses();

    /**
     * @return \ArrayObject
     */
    public function getInterfaces();

    /**
     * @return \ArrayObject
     */
    public function getTraits();

    /**
     * @return \ArrayObject
     */
    public function getErrors();
}
