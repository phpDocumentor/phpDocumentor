<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;

/**
 * Public interface definition for object representing traits.
 */
interface TraitInterface extends ElementInterface, TypeInterface
{
    /**
     * Sets the properties associated with this trait.
     *
     * @param Collection<PropertyDescriptor> $properties
     */
    public function setProperties(Collection $properties) : void;

    /**
     * Returns the properties associated with this trait.
     *
     * @return Collection<PropertyDescriptor>
     */
    public function getProperties() : Collection;

    /**
     * Returns all properties inherited from parent traits.
     *
     * @return Collection<PropertyDescriptor>
     */
    public function getInheritedProperties() : Collection;

    /**
     * Sets all methods belonging to this trait.
     *
     * @param Collection<MethodDescriptor> $methods
     */
    public function setMethods(Collection $methods) : void;

    /**
     * Returns all methods belonging to this trait.
     *
     * @return Collection<MethodDescriptor>
     */
    public function getMethods() : Collection;

    /**
     * Returns a list of all methods inherited from parent traits.
     *
     * @return Collection<MethodDescriptor>
     */
    public function getInheritedMethods() : Collection;
}
