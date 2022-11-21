<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;

trait HasDescription
{
    protected ?DescriptionDescriptor $description = null;

    /**
     * Sets a description or none if there is no description.
     */
    public function setDescription(?DescriptionDescriptor $description): void
    {
        $this->description = $description;
    }

    /**
     * Returns the description for this element.
     *
     * This method will automatically attempt to inherit the parent's description if this one has none.
     */
    public function getDescription(): ?DescriptionDescriptor
    {
        if ($this->description !== null) {
            return $this->description;
        }

        if ($this instanceof DescriptorAbstract) {
            $parentElement = $this->getInheritedElement();
            if ($parentElement instanceof self) {
                return $parentElement->getDescription();
            }
        }

        return null;
    }
}
