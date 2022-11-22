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

use phpDocumentor\Reflection\Fqsen;

/**
 * Describes the public interface for any descriptor that is the child is another.
 */
interface ChildInterface
{
    /**
     * Returns the parent for this descriptor.
     *
     * @return ElementInterface|Fqsen|string|null
     */
    public function getParent();

    /**
     * Sets the parent for this Descriptor.
     *
     * @param ElementInterface|Fqsen|string|null $parent
     */
    public function setParent($parent): void;
}
