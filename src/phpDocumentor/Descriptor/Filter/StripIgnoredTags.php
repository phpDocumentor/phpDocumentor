<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Descriptor\TagDescriptor;

use function in_array;

final class StripIgnoredTags implements FilterInterface
{
    public function __invoke(FilterPayload $payload): FilterPayload
    {
        if (!$payload->getFilterable() instanceof TagDescriptor) {
            return $payload;
        }

        if (in_array($payload->getFilterable()->getName(), $payload->getApiSpecification()->getIgnoredTags())) {
            return new FilterPayload(null, $payload->getApiSpecification());
        }

        return $payload;
    }
}
