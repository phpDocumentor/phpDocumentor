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
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

/**
 * Strips any Descriptor if the ignore tag is present with that element.
 */
class StripIgnore implements FilterInterface
{
    /** @var ProjectDescriptorBuilder $builder */
    protected $builder;

    /**
     * Initializes this filter with an instance of the builder to retrieve the latest ProjectDescriptor from.
     */
    public function __construct(ProjectDescriptorBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Filter Descriptor with ignore tags.
     */
    public function __invoke(?DescriptorAbstract $value) : ?DescriptorAbstract
    {
        if ($value !== null && $value->getTags()->fetch('ignore')) {
            return null;
        }

        return $value;
    }
}
