<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Reflection\Fqsen;

trait HasFqsen
{
    /** @var Fqsen Fully Qualified Structural Element Name; the FQCN including method, property or constant name */
    protected Fqsen $fqsen;

    /**
     * Sets the Fully Qualified Structural Element Name (FQSEN) for this element.
     *
     * @internal should not be called by any other class than the assemblers
     */
    public function setFullyQualifiedStructuralElementName(Fqsen $name): void
    {
        $this->fqsen = $name;
    }

    /**
     * Returns the Fully Qualified Structural Element Name (FQSEN) for this element.
     */
    public function getFullyQualifiedStructuralElementName(): ?Fqsen
    {
        return $this->fqsen;
    }
}
