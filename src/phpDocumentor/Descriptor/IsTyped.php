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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Reflection\Type;

interface IsTyped
{
    /**
     * Sets a normalized list of types that the argument represents.
     *
     * Arguments should have one of the types mentioned in this array. If this array is empty than that is considered
     * to be the type `mixed` (meaning: can be anything).
     *
     * Any Type representing a class/interface/trait should be normalized to its complete FQCN, including preceding
     * backslash. Types that do not represent a class/interface/trait should be written in lowercaps and should not be
     * preceded by a backslash.
     *
     * @link https://github.com/phpDocumentor/phpDocumentor2/blob/develop/docs/PSR.md#appendix-a-types Definition of a
     *     type.
     *
     * @param ?Type $type Type of this agument represented as a reflection type.
     *
     * @todo update link to point to the final destination for the PHPDoc Standard.
     */
    public function setType(Type|null $type): void;

    /**
     * Returns a normalized Types.
     *
     * @see self::setTypes() for details on what types represent.
     */
    public function getType(): Type|null;
}
