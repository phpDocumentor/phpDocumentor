<?php
/**
 * phpDocumentor2
 */

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use Zend\Filter\AbstractFilter;

class stripInternal extends AbstractFilter
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
     * If the ProjectDescriptor's settings allow internal tags then return the Descriptor, otherwise null to filter it.
     *
     * @param DescriptorAbstract $value
     *
     * @return DescriptorAbstract|null
     */
    public function filter($value)
    {
        // if internal elements are not allowed; filter this element
        if ($value->getTags()->get('internal') && !$this->builder->isVisibilityAllowed(Settings::VISIBILITY_INTERNAL)) {
            return null;
        }

        return $value;
    }
}
