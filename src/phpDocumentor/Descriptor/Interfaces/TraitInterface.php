<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
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
     */
    public function setProperties(Collection $properties) : void;

    /**
     * Returns the properties associated with this trait.
     */
    public function getProperties() : Collection;

    /**
     * Returns all properties inherited from parent traits.
     */
    public function getInheritedProperties() : Collection;

    /**
     * Sets all methods belonging to this trait.
     */
    public function setMethods(Collection $methods) : void;

    /**
     * Returns all methods belonging to this trait.
     */
    public function getMethods() : Collection;

    /**
     * Returns a list of all methods inherited from parent traits.
     */
    public function getInheritedMethods() : Collection;
}
