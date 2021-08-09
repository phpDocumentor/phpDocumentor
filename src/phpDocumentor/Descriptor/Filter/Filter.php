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

use League\Pipeline\InterruptibleProcessor;
use League\Pipeline\Pipeline;
use phpDocumentor\Configuration\ApiSpecification;

/**
 * Filter used to manipulate a descriptor after being build.
 *
 * This class is used during the building of descriptors. It passes the descriptor to each individual sub-filter, which
 * may change data in the descriptor or even remove it from the building process by returning null.
 */
class Filter
{
    /** @var Pipeline */
    private $pipeline;

    /**
     * Constructs the filter pipeline.
     *
     * Filters are allowed to return null when a elements needs to be removed. Therefor a
     * default InterruptibleProcessor processor is applied which prevents the errors in these situations.
     *
     * @param iterable<int, FilterInterface> $filters
     */
    public function __construct(iterable $filters)
    {
        $nullInteruption = new InterruptibleProcessor(static function (FilterPayload $value) {
            return $value->getFilterable() !== null;
        });

        $this->pipeline = new Pipeline($nullInteruption, ...$filters);
    }

    /**
     * Filters the given Descriptor and returns the altered object.
     *
     * @param TDescriptor $descriptor
     *
     * @return TDescriptor|null
     *
     * @template TDescriptor as Filterable
     */
    public function filter(Filterable $descriptor, ApiSpecification $apiSpecification): ?Filterable
    {
        return $this->pipeline->process(new FilterPayload($descriptor, $apiSpecification))->getFilterable();
    }
}
