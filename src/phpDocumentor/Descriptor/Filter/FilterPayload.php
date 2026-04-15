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
