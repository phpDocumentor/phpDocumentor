<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Descriptor;

interface PropertyHookInterface extends DocblockInterface, AttributedInterface, Descriptor
{
    /**
     * Returns the name of a property hook.
     *
     * @return 'get' | 'set'
     */
    public function getName(): string;

    /** @return Collection<ArgumentInterface> */
    public function getArguments(): Collection;
}
