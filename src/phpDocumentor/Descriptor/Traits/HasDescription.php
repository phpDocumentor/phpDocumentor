<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\Interfaces\InheritsFromElement;

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

trait HasDescription
{
    protected DescriptionDescriptor|null $description = null;

    /**
     * Sets a description or none to inherit from a parent.
     */
    public function setDescription(DescriptionDescriptor|null $description): void
    {
        $this->description = $description;
    }

    /**
     * Returns the description for this element.
     *
     * This method will automatically attempt to inherit the parent's description if this one has none.
     */
    public function getDescription(): DescriptionDescriptor
    {
        if ($this->description !== null) {
            return $this->description;
        }

        if ($this instanceof InheritsFromElement) {
            $parentElement = $this->getInheritedElement();
            if ($parentElement instanceof self) {
                return $parentElement->getDescription();
            }
        }

        return DescriptionDescriptor::createEmpty();
    }
}
