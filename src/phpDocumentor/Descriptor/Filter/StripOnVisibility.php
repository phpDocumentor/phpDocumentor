<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Filter;

use InvalidArgumentException;
use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\Interfaces\VisibilityInterface;

/**
 * Strips any Descriptor if their visibility is allowed according to the ProjectDescriptorBuilder.
 */
class StripOnVisibility implements FilterInterface
{
    /**
     * Filter Descriptor with based on visibility.
     */
    public function __invoke(FilterPayload $payload): FilterPayload
    {
        if (!$payload->getFilterable() instanceof DescriptorAbstract) {
            return $payload;
        }

        $filterable = $payload->getFilterable();

        // if a Descriptor is marked as 'api' and this is set as a visibility; _always_ show it; even if the visibility
        // is not set
        if (
            isset($filterable->getTags()['api'])
            && $payload->getApiSpecification()->isVisibilityAllowed(ApiSpecification::VISIBILITY_API)
        ) {
            return $payload;
        }

        if (!$filterable instanceof VisibilityInterface) {
            return $payload;
        }

        if ($payload->getApiSpecification()->isVisibilityAllowed($this->toVisibility($filterable->getVisibility()))) {
            return $payload;
        }

        return new FilterPayload(null, $payload->getApiSpecification());
    }

    private function toVisibility(string $visibility): int
    {
        switch ($visibility) {
            case 'public':
                return ApiSpecification::VISIBILITY_PUBLIC;

            case 'protected':
                return ApiSpecification::VISIBILITY_PROTECTED;

            case 'private':
                return ApiSpecification::VISIBILITY_PRIVATE;
        }

        throw new InvalidArgumentException($visibility . ' is not a valid visibility');
    }
}
