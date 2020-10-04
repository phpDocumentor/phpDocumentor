<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder;

use phpDocumentor\Descriptor\Descriptor;

/**
 * A step in creating descriptors from reflection elements
 *
 * A reducer can take over part of building a descriptor from the step before. For example descriptions are
 * always handled the same way. There could be a reducer to create the description and set it to the input descriptor.
 */
interface AssemblerReducer
{
    /**
     * @param T|null $descriptor
     *
     * @return T|null
     *
     * @template T of Descriptor
     */
    public function create(object $data, ?Descriptor $descriptor = null) : ?Descriptor;
}
