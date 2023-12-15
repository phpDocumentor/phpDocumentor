<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Interfaces\AttributeInterface;
use phpDocumentor\Descriptor\Traits\HasDescription;
use phpDocumentor\Descriptor\Traits\HasFqsen;
use phpDocumentor\Descriptor\Traits\HasName;
use Stringable;

final class AttributeDescriptor implements Descriptor, Stringable, AttributeInterface
{
    use HasName;
    use HasDescription;
    use HasFqsen;

    public function __toString(): string
    {
        return $this->getName();
    }
}
