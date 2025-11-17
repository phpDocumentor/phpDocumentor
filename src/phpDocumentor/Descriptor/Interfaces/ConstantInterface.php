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
use phpDocumentor\Descriptor\ValueObjects\Visibility;
use phpDocumentor\Reflection\Php\Expression;

/**
 * Descriptor representing a constant on a class, trait, property or file.
 */
interface ConstantInterface extends ElementInterface, TypeInterface, ChildInterface, IsTyped, VisibilityInterface
{
    /**
     * Sets the value representation for this constant.
     */
    public function setValue(Expression $value): void;

    /**
     * Retrieves a textual representation of the value in this constant.
     */
    public function getValue(): Expression;

    public function setFinal(bool $final): void;

    public function setVisibility(Visibility $visibility): void;

    public function isFinal(): bool;

    /** @return Collection<VarDescriptor> */
    public function getVar(): Collection;
}
