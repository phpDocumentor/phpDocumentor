<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Reflection\Type;

/**
 * Describes the public interface for a descriptor of an Argument.
 */
interface ArgumentInterface extends ElementInterface
{
    /**
     * Sets a normalized list of types that the argument represents.
     *
     * Arguments should have one of the types mentioned in this array. If this array is empty than that is considered
     * to be the type `mixed` (meaning: can be anything).
     *
     * Any Type representing a class/interface/trait should be normalized to its complete FQCN, including preceding
     * backslash. Types that do not represent a class/interface/trait should be written in lowercaps and should not be
     * preceded by a backslash.
     *
     * @param ?Type $type Type of this agument represented as a reflection type.
     *
     * @link https://github.com/phpDocumentor/phpDocumentor2/blob/develop/docs/PSR.md#appendix-a-types Definition of a
     *     type.
     *
     * @todo update link to point to the final destination for the PHPDoc Standard.
     */
    public function setType(?Type $type);

    /**
     * Returns a normalized Types.
     *
     * @see self::setTypes() for details on what types represent.
     *
     * @return Type|null
     */
    public function getType(): ?Type;

    /**
     * Sets the default value for an argument expressed as a string.
     *
     * @param string $value A textual representation of the default value.
     */
    public function setDefault($value);

    /**
     * Returns the default value for an argument as string or null if no default is set.
     *
     * @return string|null A textual representation of the default value, or null if no default value is present.
     */
    public function getDefault();

    /**
     * Sets whether this argument passes its parameter by reference or by value.
     *
     * @param boolean $byReference True if the parameter is passed by reference, otherwise it is by value.
     */
    public function setByReference($byReference);

    /**
     * Returns whether the parameter is passed by reference or by value.
     *
     * @return boolean True if the parameter is passed by reference, otherwise it is by value.
     */
    public function isByReference();
}
