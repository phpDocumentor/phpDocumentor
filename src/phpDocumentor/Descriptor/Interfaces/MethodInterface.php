<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

/**
 * Describes the public interface of the description for a method.
 */
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
