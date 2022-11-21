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

use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Interfaces\InheritsFromElement;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Descriptor\Interfaces\PackageInterface;

trait HasPackage
{
    /** @var PackageInterface|string $package The package with which this element is associated */
    protected $package;

    /**
     * Sets the name of the package to which this element belongs.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param PackageInterface|string $package
     */
    public function setPackage($package): void
    {
        $this->package = $package;

        if ($this instanceof ClassInterface || $this instanceof InterfaceInterface) {
            foreach ($this->getConstants() as $constant) {
                $constant->setPackage($package);
            }

            foreach ($this->getMethods() as $method) {
                $method->setPackage($package);
            }
        }

        if (!$this instanceof ClassInterface) {
            return;
        }

        foreach ($this->getProperties() as $property) {
            $property->setPackage($package);
        }
    }

    /**
     * Returns the package name for this element.
     */
    public function getPackage(): ?PackageInterface
    {
        $inheritedElement = $this instanceof InheritsFromElement
            ? $this->getInheritedElement()
            : null;

        if (
            $this->package instanceof PackageInterface
            && !($this->package->getName() === '\\' && $inheritedElement)
        ) {
            return $this->package;
        }

        if ($inheritedElement instanceof ElementInterface) {
            return $inheritedElement->getPackage();
        }

        return null;
    }
}
