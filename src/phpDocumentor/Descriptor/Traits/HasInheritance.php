<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Reflection\Fqsen;

trait HasInheritance
{
    /** @var DescriptorAbstract|string|Fqsen|null the element from which to inherit information in this element */
    protected $inheritedElement = null;

    /**
     * Returns the element from which this element inherits, or null if it doesn't inherit any information.
     *
     * This method is usually overridden in consuming classes with the determination how that class should resolve
     * inheritance. This is a placeholder function for those classes who need a simple way to provide inheritance.
     *
     * @return DescriptorAbstract|string|Fqsen|null
     */
    public function getInheritedElement()
    {
        return $this->inheritedElement;
    }
}
