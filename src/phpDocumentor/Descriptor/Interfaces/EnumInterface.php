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
use phpDocumentor\Reflection\Type;

/**
 * Common interface representing the description of a class.
 *
 * @see NamespaceInterface Classes may be contained in namespaces.
 * @see FileInterface      Classes may be defined in a file.
 */
interface EnumInterface extends ElementInterface, TypeInterface, AttributedInterface
{
    /** @param Collection<InterfaceInterface|Fqsen> $implements */
    public function setInterfaces(Collection $implements): void;

    /** @return Collection<InterfaceInterface|Fqsen> */
    public function getInterfaces(): Collection;

    /** @return Collection<InterfaceInterface|Fqsen> */
    public function getInterfacesIncludingInherited(): Collection;

    /** @param Collection<MethodInterface> $methods */
    public function setMethods(Collection $methods): void;

    /** @return Collection<MethodInterface> */
    public function getMethods(): Collection;

    /** @return Collection<MethodInterface> */
    public function getInheritedMethods(): Collection;

    /** @param Collection<EnumCaseInterface> $cases */
    public function setCases(Collection $cases): void;

    /** @return Collection<EnumCaseInterface> */
    public function getCases(): Collection;

    public function setBackedType(Type|null $type): void;

    public function getBackedType(): Type|null;

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

    /** @param Collection<ConstantInterface> $constants */
    public function setConstants(Collection $constants): void;

    /** @return Collection<ConstantInterface> */
    public function getConstants(): Collection;
}
