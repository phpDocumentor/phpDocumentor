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

use phpDocumentor\Descriptor\Collection;

/**
 * Represents the public interface for an interface descriptor.
 */
interface InterfaceInterface extends ElementInterface, TypeInterface
{
    /**
     * Returns the parent for this descriptor.
     *
     * @return Collection
     */
    public function getParent();

    /**
     * Sets the parent for this Descriptor.
     *
     * @param Collection $parent
     */
    public function setParent($parent);

    /**
     * Sets the constants associated with this interface.
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
