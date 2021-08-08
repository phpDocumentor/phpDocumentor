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
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Reflection\Fqsen;

/**
 * Common interface representing the description of a class.
 *
 * @see NamespaceInterface Classes may be contained in namespaces.
 * @see FileInterface      Classes may be defined in a file.
 */
interface ClassInterface extends ElementInterface, ChildInterface, TypeInterface
{
    /** @param Collection<InterfaceDescriptor|Fqsen> $implements */
    public function setInterfaces(Collection $implements): void;

    /** @return Collection<InterfaceDescriptor|Fqsen> */
    public function getInterfaces(): Collection;

    public function setFinal(bool $final): void;

    public function isFinal(): bool;

    public function setAbstract(bool $abstract): void;

    public function isAbstract(): bool;

    /** @param Collection<ConstantDescriptor> $constants */
    public function setConstants(Collection $constants): void;

    /** @return Collection<ConstantDescriptor> */
    public function getConstants(): Collection;

    /** @param Collection<MethodDescriptor> $methods */
    public function setMethods(Collection $methods): void;

    /** @return Collection<MethodDescriptor> */
    public function getMethods(): Collection;

    /** @return Collection<MethodDescriptor> */
    public function getInheritedMethods(): Collection;

    /** @param Collection<PropertyDescriptor> $properties */
    public function setProperties(Collection $properties): void;

    /** @return Collection<PropertyDescriptor> */
    public function getProperties(): Collection;

    /** @return Collection<PropertyDescriptor> */
    public function getInheritedProperties(): Collection;
}
