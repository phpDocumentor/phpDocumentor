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

interface InheritsFromElement
{
    /**
     * Returns the element from which this element inherits, or null if it doesn't inherit any information.
     *
     * @return ElementInterface|string|Fqsen|null
     */
    public function getInheritedElement();
}
