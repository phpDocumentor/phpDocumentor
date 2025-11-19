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

use phpDocumentor\Descriptor\IsTyped;
use phpDocumentor\Descriptor\ValueObjects\IsApplicable;
use phpDocumentor\Reflection\Php\Expression;

/**
 * Describes the public interface for a descriptor of an Argument.
 */
interface ArgumentInterface extends ElementInterface, IsTyped
{
    /**
     * To which method does this argument belong to
     */
    public function setMethod(MethodInterface $method): void;

    public function getMethod(): MethodInterface;

    /**
     * Sets the default value for an argument expressed as a string.
     *
     * @param Expression $value A textual representation of the default value.
     */
    public function setDefault(Expression $value): void;

    /**
     * Returns the default value for an argument as string or null if no default is set.
     *
     * @return Expression|null A textual representation of the default value, or null if no default value is present.
     */
    public function getDefault(): Expression|null;

    /**
     * Sets whether this argument passes its parameter by reference or by value.
     *
     * @param IsApplicable $byReference True if the parameter is passed by reference, otherwise it is by value.
     */
    public function setByReference(IsApplicable $byReference): void;

    /**
     * Returns whether the parameter is passed by reference or by value.
     *
     * @return bool if the parameter is passed by reference, otherwise it is by value.
     */
    public function isByReference(): bool;

    /**
     * Sets whether this argument represents a variadic argument.
     */
    public function setVariadic(IsApplicable $isVariadic): void;

    /**
     * Returns whether this argument represents a variadic argument.
     */
    public function isVariadic(): bool;
}
