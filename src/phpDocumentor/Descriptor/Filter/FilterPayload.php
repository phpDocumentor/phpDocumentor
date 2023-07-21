<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Configuration\ApiSpecification;

final class FilterPayload
{
    public function __construct(
        private readonly Filterable|null $filterable,
        private readonly ApiSpecification $apiSpecification,
    ) {
    }

    public function getFilterable(): Filterable|null
    {
        return $this->filterable;
    }

    public function getApiSpecification(): ApiSpecification
    {
        return $this->apiSpecification;
    }
}
