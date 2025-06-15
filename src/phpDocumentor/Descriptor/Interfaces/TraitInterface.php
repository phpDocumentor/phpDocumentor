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
 * Public interface definition for object representing traits.
 */
interface TraitInterface extends ElementInterface, TypeInterface, AttributedInterface
{
    /** @param Collection<ConstantInterface> $constants */
    public function setConstants(Collection $constants): void;

    /** @return Collection<ConstantInterface> */
    public function getConstants(): Collection;

    /** @return Collection<ConstantInterface> */
    public function getInheritedConstants(): Collection;

    /**
     * Sets the properties associated with this trait.
     *
     * @param Collection<PropertyInterface> $properties
     */
    public function setProperties(Collection $properties): void;

    /**
     * Returns the properties associated with this trait.
     *
     * @return Collection<PropertyInterface>
     */
    public function getProperties(): Collection;

    /**
     * Returns all properties inherited from parent traits.
     *
     * @return Collection<PropertyInterface>
     */
    public function getInheritedProperties(): Collection;

    /**
     * Sets all methods belonging to this trait.
     *
     * @param Collection<MethodInterface> $methods
     */
    public function setMethods(Collection $methods): void;

    /**
     * Returns all methods belonging to this trait.
     *
     * @return Collection<MethodInterface>
     */
    public function getMethods(): Collection;

    /**
     * Returns a list of all methods inherited from parent traits.
     *
     * @return Collection<MethodInterface>
     */
    public function getInheritedMethods(): Collection;

    /**
     * Returns the file associated with the parent class or trait.
     */
    public function getFile(): FileInterface|null;

    /**
     * Sets all traits used by this trait.
     *
     * @param Collection<TraitInterface|Fqsen> $usedTraits
     */
    public function setUsedTraits(Collection $usedTraits): void;

    /**
     * Returns all traits used by this trait.
     *
     * @return Collection<TraitInterface|Fqsen>
     */
    public function getUsedTraits(): Collection;
}
