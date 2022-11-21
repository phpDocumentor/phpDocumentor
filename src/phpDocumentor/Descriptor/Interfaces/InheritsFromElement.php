<?php

declare(strict_types=1);

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
