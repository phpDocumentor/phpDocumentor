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
 * Represents the public interface for an interface descriptor.
 */
interface InterfaceInterface extends ElementInterface, ChildInterface, TypeInterface
{
    /**
     * Sets the constants associated with this interface.
     *
     * @param Collection $constants
     *
     * @return void
     */
    public function setConstants(Collection $constants);

    /**
     * Returns the constants associated with this interface.
     *
     * @return Collection
     */
    public function getConstants();

    /**
     * Sets the methods belonging to this interface.
     *
     * @param Collection $methods
     *
     * @return void
     */
    public function setMethods(Collection $methods);

    /**
     * Returns the methods belonging to this interface.
     *
     * @return Collection
     */
    public function getMethods();

    /**
     * Returns a list of all methods that were inherited from parent interfaces.
     *
     * @return Collection
     */
    public function getInheritedMethods();
}
