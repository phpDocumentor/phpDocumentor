<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Descriptor\DescriptorAbstract;

interface FilterInterface
{
    public function __invoke(?DescriptorAbstract $filterable) : ?DescriptorAbstract;
}
