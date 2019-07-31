<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;

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

    public function attachDefaults(ProjectDescriptorBuilder $descriptorBuilder): void
    {
        $stripOnVisibility = new StripOnVisibility($descriptorBuilder);
        $filtersOnAllDescriptors = [
            new StripInternal($descriptorBuilder),
            new StripIgnore($descriptorBuilder),
        ];

        foreach ($filtersOnAllDescriptors as $filter) {
            $this->attach(ConstantDescriptor::class, $filter);
            $this->attach(FunctionDescriptor::class, $filter);
            $this->attach(InterfaceDescriptor::class, $filter);
            $this->attach(TraitDescriptor::class, $filter);
            $this->attach(PropertyDescriptor::class, $filter);
            $this->attach(MethodDescriptor::class, $filter);
        }

        $this->attach(PropertyDescriptor::class, $stripOnVisibility);
        $this->attach(MethodDescriptor::class, $stripOnVisibility);
    }

    /**
     * Attaches a filter to a specific FQCN.
     */
    public function attach(string $fqcn, FilterInterface $filter): void
    {
        $this->factory->attachTo($fqcn, $filter);
    }

    /**
     * Filters the given Descriptor and returns the altered object.
     */
    public function filter(Filterable $descriptor): ?Filterable
    {
        $chain = $this->factory->getChainFor(get_class($descriptor));

        return $chain($descriptor);
    }
}
