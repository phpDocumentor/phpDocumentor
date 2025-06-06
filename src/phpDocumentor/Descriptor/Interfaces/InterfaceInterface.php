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
use phpDocumentor\Reflection\Fqsen;

/**
 * Represents the public interface for an interface descriptor.
 */
interface InterfaceInterface extends ElementInterface, TypeInterface, AttributedInterface
{
    /**
     * Returns the parent for this descriptor.
     *
     * @return Collection<InterfaceInterface|Fqsen>
     */
    public function getParent(): Collection;

    /**
     * Sets the parent for this Descriptor.
     *
     * @param Collection<InterfaceInterface|Fqsen> $parents
     */
    public function setParent(Collection $parents): void;

    /**
     * Sets the constants associated with this interface.
     *
     * @param Collection<ConstantInterface> $constants
     */
    public function setConstants(Collection $constants): void;

    /**
     * Returns the constants associated with this interface.
     *
     * @return Collection<ConstantInterface>
     */
    public function getConstants(): Collection;

    /** @return Collection<ConstantInterface> */
    public function getInheritedConstants(): Collection;

    /**
     * Sets the methods belonging to this interface.
     *
     * @param Collection<MethodInterface> $methods
     */
    public function setMethods(Collection $methods): void;

    /**
     * Returns the methods belonging to this interface.
     *
     * @return Collection<MethodInterface>
     */
    public function getMethods(): Collection;

    /**
     * Returns a list of all methods that were inherited from parent interfaces.
     *
     * @return Collection<MethodInterface>
     */
    public function getInheritedMethods(): Collection;
}
