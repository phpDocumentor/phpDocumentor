<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Reflection\Type;

/**
 * Descriptor representing a property on a class or trait.
 */
interface PropertyInterface extends ElementInterface, TypeInterface
{
    /**
     * Stores a textual representation of the default value for a property.
     *
     * @param string $default
     */
    public function setDefault($default);

    /**
     * Returns the textual representation of the default value for a property.
     *
     * @return string
     */
    public function getDefault();

    /**
     * Sets whether this property is static in scope.
     *
     * @param boolean $static
     */
    public function setStatic($static);

    /**
     * Returns whether this property is static in scope.
     *
     * @return boolean
     */
    public function isStatic();

    /**
     * Sets the types associated with the value(s) for this property.
     */
    public function setTypes(Type $types);

    /**
     * Returns the types associated with the value(s) for this property.
     *
     * @return string[]
     */
    public function getTypes();

    /**
     * Sets whether this property is available from inside or outside its class and/or descendants.
     *
     * @param string $visibility May be either 'public', 'private' or 'protected'.
     *
     * @return string
     */
    public function setVisibility($visibility);
}
