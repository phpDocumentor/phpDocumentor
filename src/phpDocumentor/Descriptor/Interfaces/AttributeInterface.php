<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ValueObjects\CallArgument;
use phpDocumentor\Reflection\Fqsen;

interface AttributeInterface
{
    public function getName(): string;

    /**
     * Sets the Fully Qualified Structural Element Name (FQSEN) for this element.
     */
    public function setFullyQualifiedStructuralElementName(Fqsen $name): void;

    /**
     * Returns the Fully Qualified Structural Element Name (FQSEN) for this element.
     */
    public function getFullyQualifiedStructuralElementName(): Fqsen|null;

    public function addArgument(CallArgument $argument): void;

    /** @return Collection<CallArgument> */
    public function getArguments(): Collection;

    public function hasArguments(): bool;
}
