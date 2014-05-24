<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\Interfaces\VisibilityInterface;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use Zend\Filter\AbstractFilter;

/**
 * Strips any Descriptor if their visibility is allowed according to the ProjectDescriptorBuilder.
 */
class StripOnVisibility extends AbstractFilter
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
     * Filter Descriptor with based on visibility.
     *
     * @param DescriptorAbstract $value
     *
     * @return DescriptorAbstract|null
     */
    public function filter($value)
    {
        if ($value instanceof VisibilityInterface
            && !$this->builder->isVisibilityAllowed($value->getVisibility())
        ) {
            return null;
        }

        return $value;
    }
}
