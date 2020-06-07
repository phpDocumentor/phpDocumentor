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
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Reflection\Fqsen;

/**
 * Represents the public interface for an interface descriptor.
 */
interface InterfaceInterface extends ElementInterface, TypeInterface
{
    /**
     * Returns the parent for this descriptor.
     *
     * @return Collection<InterfaceDescriptor|Fqsen>
     */
    public function getParent() : Collection;

    /**
     * Sets the parent for this Descriptor.
     *
     * @param Collection<InterfaceDescriptor|Fqsen> $parents
     */
    public function setParent(Collection $parents) : void;

    /**
     * Sets the constants associated with this interface.
     *
     * @param Collection<ConstantDescriptor> $constants
     */
    public function setConstants(Collection $constants) : void;

    /**
     * Returns the constants associated with this interface.
     *
     * @return Collection<ConstantDescriptor>
     */
    public function getConstants() : Collection;

    /**
     * Sets the methods belonging to this interface.
     *
     * @param Collection<MethodDescriptor> $methods
     */
    public function setMethods(Collection $methods) : void;

    /**
     * Returns the methods belonging to this interface.
     *
     * @return Collection<MethodDescriptor>
     */
    public function getMethods() : Collection;

    /**
     * Returns a list of all methods that were inherited from parent interfaces.
     *
     * @return Collection<MethodDescriptor>
     */
    public function getInheritedMethods() : Collection;
}
