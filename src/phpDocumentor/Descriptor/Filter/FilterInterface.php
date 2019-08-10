<?php

namespace phpDocumentor\Descriptor\Filter;

interface FilterInterface
{
    public function __invoke(?Filterable $filterable) : ?Filterable;
}
