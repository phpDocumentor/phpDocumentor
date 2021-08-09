<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Configuration\ApiSpecification;

final class FilterPayload
{
    /** @var Filterable|null */
    private $filterable;

    /** @var ApiSpecification */
    private $apiSpecification;

    public function __construct(?Filterable $filterable, ApiSpecification $apiSpecification)
    {
        $this->filterable = $filterable;
        $this->apiSpecification = $apiSpecification;
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
