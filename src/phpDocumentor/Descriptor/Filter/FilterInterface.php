<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Filter;

interface FilterInterface
{
    public function __invoke(Filterable $filterable) : ?Filterable;
}
