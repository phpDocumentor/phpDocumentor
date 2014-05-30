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
 * Public interface definition for object representing traits.
 */
interface TraitInterface extends ElementInterface, TypeInterface
{
    /**
     * Sets the properties associated with this trait.
     *
     * @param Collection $properties
     *
     * @return void
     */
    public function setProperties(Collection $properties);

    /**
     * Returns the properties associated with this trait.
     *
     * @return Collection
     */
    public function getProperties();

    /**
     * Returns all properties inherited from parent traits.
     *
     * @return Collection
     */
    public function getInheritedProperties();

    /**
     * Sets all methods belonging to this trait.
     *
     * @param Collection $methods
     *
     * @return void
     */
    public function setMethods(Collection $methods);

    /**
     * Returns all methods belonging to this trait.
     *
     * @return Collection
     */
    public function getMethods();

    /**
     * Returns a list of all methods inherited from parent traits.
     *
     * @return Collection
     */
    public function getInheritedMethods();
}
