<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Reflection\Type;

/**
 * Descriptor representing a property on a class or trait.
 */
interface PropertyInterface extends ElementInterface, TypeInterface
{
    /**
     * Stores a textual representation of the default value for a property.
     */
    public function setDefault(string $default) : void;

    /**
     * Returns the textual representation of the default value for a property, or null if none is provided.
     */
    public function getDefault() : ?string;

    /**
     * Sets whether this property is static in scope.
     */
    public function setStatic(bool $static) : void;

    /**
     * Returns whether this property is static in scope.
     */
    public function isStatic() : bool;

    /**
     * Sets the types associated with the value(s) for this property.
     */
    public function setType(Type $type) : void;

    /**
     * Returns the types associated with the value(s) for this property.
     */
    public function getType() : ?Type;

    /**
     * Sets whether this property is available from inside or outside its class and/or descendants.
     *
     * @param string $visibility May be either 'public', 'private' or 'protected'.
     */
    public function setVisibility(string $visibility) : void;
}
