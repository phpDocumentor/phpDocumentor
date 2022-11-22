<?php

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Validation\Error;

interface TracksErrors
{
    /**
     * @return Collection<Error>
     */
    public function getErrors(): Collection;
}
