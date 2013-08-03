<?php
/**
 * phpDocumentor2
 */

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use Zend\Filter\AbstractFilter;

class StripIgnore extends AbstractFilter
{
    /** @var ProjectDescriptorBuilder $builder */
    protected $builder;

    /**
     * Initializes this filter with an instance of the builder to retrieve the latest ProjectDescriptor from.
     *
     * @param ProjectDescriptorBuilder $builder
     */
    public function __construct(ProjectDescriptorBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Filter Descriptor with ignore tags.
     *
     * @param DescriptorAbstract $value
     *
     * @return DescriptorAbstract|null
     */
    public function filter($value)
    {
        if (!is_null($value) && $value->getTags()->get('ignore')) {
            return null;
        }

        return $value;
    }
}
