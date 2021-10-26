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
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Reflection\Fqsen;

/**
 * Common interface representing the description of a class.
 *
 * @see NamespaceInterface Classes may be contained in namespaces.
 * @see FileInterface      Classes may be defined in a file.
 */
interface EnumInterface extends ElementInterface, TypeInterface
{
    /** @param Collection<InterfaceDescriptor|Fqsen> $implements */
    public function setInterfaces(Collection $implements): void;

    /** @return Collection<InterfaceDescriptor|Fqsen> */
    public function getInterfaces(): Collection;

    /** @param Collection<MethodDescriptor> $methods */
    public function setMethods(Collection $methods): void;

    /** @return Collection<MethodDescriptor> */
    public function getMethods(): Collection;

    /** @return Collection<MethodDescriptor> */
    public function getInheritedMethods(): Collection;

    /** @param Collection<EnumCaseInterface> $cases */
    public function setCases(Collection $cases): void;

    /** @return Collection<EnumCaseInterface> */
    public function getCases(): Collection;
}
