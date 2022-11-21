<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\PackageDescriptor;

trait HasPackage
{
    /** @var PackageDescriptor|string $package The package with which this element is associated */
    protected $package;

    /**
     * Sets the name of the package to which this element belongs.
     *
     * @internal should not be called by any other class than the assamblers
     *
     * @param PackageDescriptor|string $package
     */
    public function setPackage($package): void
    {
        $this->package = $package;
    }

    /**
     * Returns the package name for this element.
     */
    public function getPackage(): ?PackageDescriptor
    {
        $inheritedElement = $this instanceof DescriptorAbstract
            ? $this->getInheritedElement()
            : null;

        if (
            $this->package instanceof PackageDescriptor
            && !($this->package->getName() === '\\' && $inheritedElement)
        ) {
            return $this->package;
        }

        if ($inheritedElement instanceof self) {
            return $inheritedElement->getPackage();
        }

        return null;
    }
}
