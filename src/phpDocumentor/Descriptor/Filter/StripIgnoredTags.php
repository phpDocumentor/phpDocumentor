<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Descriptor\TagDescriptor;
use function in_array;

final class StripIgnoredTags implements FilterInterface
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

    public function __invoke(Filterable $filterable) : ?Filterable
    {
        if (!$filterable instanceof TagDescriptor) {
            return $filterable;
        }

        if (in_array($filterable->getName(), $this->builder->getIgnoredTags())) {
            return null;
        }

        return $filterable;
    }
}
