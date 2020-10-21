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

use phpDocumentor\Descriptor\DescriptorAbstract;

/**
 * Strips any Descriptor if the ignore tag is present with that element.
 */
class StripIgnore implements FilterInterface
{
    /**
     * Filter Descriptor with ignore tags.
     */
    public function __invoke(Filterable $value) : ?Filterable
    {
        if (!$value instanceof DescriptorAbstract) {
            return $value;
        }

        if ($value->getTags()->fetch('ignore')) {
            return null;
        }

        return $value;
    }
}
