<?php
namespace phpDocumentor\Descriptor\Interfaces;

interface ClassInterface extends BaseInterface
{
    public function setParentClass($extends);

    public function getParentClass();

    public function setFinal($final);

    public function isFinal();

    public function setAbstract($abstract);

    public function isAbstract();

    /**
     * @return \ArrayObject
     */
    public function getConstants();

    /**
     * @return \ArrayObject
     */
    public function getInterfaces();

    /**
     * @return \ArrayObject
     */
    public function getMethods();

    /**
     * @return \ArrayObject
     */
    public function getProperties();
}