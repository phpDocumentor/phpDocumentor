<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ClassDescriptor;

interface ClassInterface extends BaseInterface
{
    /**
     * Sets the reference to a superclass for this class.
     *
     * @param ClassDescriptor $extends
     *
     * @return void
     */
    public function setParentClass($extends);

    public function getParentClass();

    public function setInterfaces(Collection $interfaces);

    /**
     * @return Collection
     */
    public function getInterfaces();

    public function setFinal($final);

    public function isFinal();

    public function setAbstract($abstract);

    public function isAbstract();

    public function setConstants(Collection $constants);

    /**
     * @return Collection
     */
    public function getConstants();

    public function setMethods(Collection $methods);

    /**
     * @return Collection
     */
    public function getMethods();

    public function setProperties(Collection $properties);

    /**
     * @return Collection
     */
    public function getProperties();
}
