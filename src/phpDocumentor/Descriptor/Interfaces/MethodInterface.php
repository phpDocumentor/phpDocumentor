<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

/**
 * Describes the public interface of the description for a method.
 */
interface MethodInterface extends ElementInterface, TypeInterface
{
    /**
     * @param boolean $abstract
     * @return void
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
     * @return void
     */
    public function setFinal($final);

    /**
     * @return boolean
     */
    public function isFinal();

    /**
     * @param boolean $static
     * @return void
     */
    public function setStatic($static);

    /**
     * @return boolean
     */
    public function isStatic();

    /**
     * @param string $visibility
     * @return void
     */
    public function setVisibility($visibility);
}
