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

use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Descriptor\DescriptorAbstract;

/**
 * Filters a Descriptor when the @internal inline tag, or normal tag, is used.
 *
 * When a Descriptor's description contains the inline tag @internal then the description of that tag should be
 * included only when the visibility allows INTERNAL information. Otherwise it needs to be removed.
 *
 * Similarly, whenever the normal @internal tag is used should this filter return null if the visibility does not allow
 * INTERNAL information. This will remove this descriptor from the project.
 *
 * @link https://docs.phpdoc.org/latest/references/phpdoc/tags/internal.html
 */
class StripInternal implements FilterInterface
{
    /**
     * If the ProjectDescriptor's settings allow internal tags then return the Descriptor, otherwise null to filter it.
     */
    public function __invoke(FilterPayload $payload): FilterPayload
    {
        $isInternalAllowed = $payload->getApiSpecification()->isVisibilityAllowed(
            ApiSpecification::VISIBILITY_INTERNAL
        );

        if ($isInternalAllowed) {
            return $payload;
        }

        $filterable = $payload->getFilterable();
        if ($filterable === null) {
            return $payload;
        }

        if ($filterable->getDescription() !== null) {
            // remove inline @internal tags
            foreach ($filterable->getDescription()->getTags() as $position => $tag) {
                if ($tag->getName() !== 'internal') {
                    continue;
                }

                $filterable->getDescription()->replaceTag($position, null);
            }
        }

        if ($filterable instanceof DescriptorAbstract) {
            // if internal elements are not allowed; filter this element
            if ($filterable->getTags()->fetch('internal')) {
                return new FilterPayload(null, $payload->getApiSpecification());
            }
        }

        return $payload;
    }
}
