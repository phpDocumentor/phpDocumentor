<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

interface MethodInterface extends BaseInterface
{
    /**
     * @param boolean $abstract
     */
    public function setAbstract($abstract);

    /**
     * @return boolean
     */
    public function isAbstract();

    /**
     * @return Collection
     */
    public function getArguments();

    /**
     * @param boolean $final
     */
    public function setFinal($final);

    /**
     * @return boolean
     */
    public function isFinal();

    /**
     * @param boolean $static
     */
    public function setStatic($static);

    /**
     * @return boolean
     */
    public function isStatic();

    /**
     * @param string $visibility
     */
    public function setVisibility($visibility);

    /**
     * @return string
     */
    public function getVisibility();
}
