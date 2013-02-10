<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

interface ClassInterface extends BaseInterface
{
    public function setParentClass($extends);

    public function getParentClass();

    public function setFinal($final);

    public function isFinal();

    public function setAbstract($abstract);

    public function isAbstract();

    /**
     * @return Collection
     */
    public function getConstants();

    /**
     * @return Collection
     */
    public function getInterfaces();

    /**
     * @return Collection
     */
    public function getMethods();

    /**
     * @return Collection
     */
    public function getProperties();
}