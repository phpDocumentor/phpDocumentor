<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use Zend\Filter\FilterInterface;

/**
 * Filter used to manipulate a descriptor after being build.
 *
 * This class is used during the building of descriptors. It passes the descriptor to each individual sub-filter, which
 * may change data in the descriptor or even remove it from the building process by returning null.
 */
class Filter
{
    /** @var int default priority for a filter in the series of filters. */
    const DEFAULT_PRIORITY = 1000;

    /** @var ClassFactory */
    protected $factory;

    /**
     * Constructs the filter and attaches the factory to it.
     */
    public function __construct(ClassFactory $factory)
    {
        $this->factory = $factory;
    }

    public function attachDefaults(ProjectDescriptorBuilder $descriptorBuilder)
    {
        $stripOnVisibility = new StripOnVisibility($descriptorBuilder);
        $filtersOnAllDescriptors = [
            new StripInternal($descriptorBuilder),
            new StripIgnore($descriptorBuilder),
        ];

        foreach ($filtersOnAllDescriptors as $filter) {
            $this->attach('phpDocumentor\Descriptor\ConstantDescriptor', $filter);
            $this->attach('phpDocumentor\Descriptor\FunctionDescriptor', $filter);
            $this->attach('phpDocumentor\Descriptor\InterfaceDescriptor', $filter);
            $this->attach('phpDocumentor\Descriptor\TraitDescriptor', $filter);
            $this->attach('phpDocumentor\Descriptor\PropertyDescriptor', $filter);
            $this->attach('phpDocumentor\Descriptor\MethodDescriptor', $filter);
        }

        $this->attach('phpDocumentor\Descriptor\PropertyDescriptor', $stripOnVisibility);
        $this->attach('phpDocumentor\Descriptor\MethodDescriptor', $stripOnVisibility);
    }

    /**
     * Attaches a filter to a specific FQCN.
     *
     * @param string          $fqcn
     * @param FilterInterface $filter
     * @param int             $priority [1000]
     */
    public function attach($fqcn, $filter, $priority = self::DEFAULT_PRIORITY)
    {
        $chain = $this->factory->getChainFor($fqcn);
        $chain->attach($filter, $priority);
    }

    /**
     * Filters the given Descriptor and returns the altered object.
     *
     * @return Filterable|null
     */
    public function filter(Filterable $descriptor)
    {
        $chain = $this->factory->getChainFor(get_class($descriptor));

        return $chain->filter($descriptor);
    }
}
