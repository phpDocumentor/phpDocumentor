<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
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
     */
    public function getParent() : ?Collection;

    /**
     * Sets the parent for this Descriptor.
     */
    public function setParent(Collection $parent) : void;

    /**
     * Sets the constants associated with this interface.
     */
    public function setConstants(Collection $constants) : void;

    /**
     * Returns the constants associated with this interface.
     */
    public function getConstants() : Collection;

    /**
     * Sets the methods belonging to this interface.
     */
    public function setMethods(Collection $methods) : void;

    /**
     * Returns the methods belonging to this interface.
     */
    public function getMethods() : Collection;

    /**
     * Returns a list of all methods that were inherited from parent interfaces.
     */
    public function getInheritedMethods() : Collection;
}
