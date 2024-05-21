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
use phpDocumentor\Descriptor\IsTyped;
use phpDocumentor\Descriptor\Tag\VarDescriptor;

/**
 * Descriptor representing a property on a class or trait.
 */
interface PropertyInterface extends ElementInterface, TypeInterface, ChildInterface, IsTyped, AttributedInterface
{
    /**
     * Returns true when this property is intended to be read-only.
     *
     * @link https://docs.phpdoc.org/latest/references/phpdoc/tags/property-read.html
     */
    public function isReadOnly(): bool;

    /**
     * Returns true when this property is intended to be write-only.
     *
     * @link https://docs.phpdoc.org/latest/references/phpdoc/tags/property-write.html
     */
    public function isWriteOnly(): bool;

    /**
     * Stores a textual representation of the default value for a property.
     */
    public function setDefault(string $value): void;

    /**
     * Returns the textual representation of the default value for a property, or null if none is provided.
     */
    public function getDefault(): string|null;

    /**
     * Sets whether this property is static in scope.
     */
    public function setStatic(bool $static): void;

    /**
     * Returns whether this property is static in scope.
     */
    public function isStatic(): bool;

    /**
     * Sets whether this property is available from inside or outside its class and/or descendants.
     *
     * @param string $visibility May be either 'public', 'private' or 'protected'.
     */
    public function setVisibility(string $visibility): void;

    /** @return Collection<VarDescriptor> */
    public function getVar(): Collection;
}
