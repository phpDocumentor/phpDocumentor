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
 * Common interface representing the description of a class.
 *
 * @see NamespaceInterface Classes may be contained in namespaces.
 * @see FileInterface      Classes may be defined in a file.
 */
interface ClassInterface extends ElementInterface, ChildInterface, TypeInterface
{
    /** @param Collection<InterfaceInterface|Fqsen> $implements */
    public function setInterfaces(Collection $implements): void;

    /** @return Collection<InterfaceInterface|Fqsen> */
    public function getInterfaces(): Collection;

    /** @return Collection<InterfaceInterface|Fqsen> */
    public function getInterfacesIncludingInherited(): Collection;

    public function setFinal(bool $final): void;

    public function isFinal(): bool;

    public function setAbstract(bool $abstract): void;

    public function isAbstract(): bool;

    /** @param Collection<ConstantInterface> $constants */
    public function setConstants(Collection $constants): void;

    /** @return Collection<ConstantInterface> */
    public function getConstants(): Collection;

    /** @return Collection<ConstantInterface> */
    public function getInheritedConstants(): Collection;

    /** @param Collection<MethodInterface> $methods */
    public function setMethods(Collection $methods): void;

    /** @return Collection<MethodInterface> */
    public function getMethods(): Collection;

    /** @return Collection<MethodInterface> */
    public function getInheritedMethods(): Collection;

    /** @param Collection<PropertyInterface> $properties */
    public function setProperties(Collection $properties): void;

    /** @return Collection<PropertyInterface> */
    public function getProperties(): Collection;

    /** @return Collection<PropertyInterface> */
    public function getInheritedProperties(): Collection;

    /**
     * Returns the file associated with the parent class or trait.
     */
    public function getFile(): FileInterface|null;

    /**
     * Sets a collection of all traits used by this class.
     *
     * @param Collection<TraitInterface|Fqsen> $usedTraits
     */
    public function setUsedTraits(Collection $usedTraits): void;

    /**
     * Returns the traits used by this class.
     *
     * Returned values may either be a string (when the Trait is not in this project) or a TraitDescriptor.
     *
     * @return Collection<TraitInterface|Fqsen>
     */
    public function getUsedTraits(): Collection;
}
