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

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\EnumInterface;
use phpDocumentor\Descriptor\Interfaces\InheritsFromElement;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Reflection\Fqsen;

trait ImplementsInterfaces
{
    /**
     * References to interfaces that are implemented by this class.
     *
     * @var Collection<InterfaceInterface|Fqsen> $implements
     */
    protected Collection $implements;

    /**
     * @internal should not be called by any other class than the assamblers
     *
     * @param Collection<InterfaceInterface|Fqsen> $implements
     */
    public function setInterfaces(Collection $implements): void
    {
        $this->implements = $implements;
    }

    /** @return Collection<InterfaceInterface|Fqsen> */
    public function getInterfaces(): Collection
    {
        if (! isset($this->implements)) {
            $this->implements = new Collection();
        }

        return $this->implements;
    }

    /** @return Collection<InterfaceInterface|Fqsen> */
    public function getInterfacesIncludingInherited(): Collection
    {
        $interfaces = $this->getInterfaces();
        if ($this instanceof InheritsFromElement) {
            $inheritedElement = $this->getInheritedElement();

            if ($inheritedElement instanceof ClassInterface || $inheritedElement instanceof EnumInterface) {
                $interfaces = $interfaces->merge($inheritedElement->getInterfacesIncludingInherited());
            }
        }

        return $interfaces;
    }
}
