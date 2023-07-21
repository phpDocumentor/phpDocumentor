<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Configuration\ApiSpecification;

final class FilterPayload
{
    public function __construct(private readonly ?Filterable $filterable, private readonly ApiSpecification $apiSpecification)
    {
    }

    public function getFilterable(): ?Filterable
    {
        return $this->filterable;
    }

    public function getApiSpecification(): ApiSpecification
    {
        return $this->apiSpecification;
    }
}
