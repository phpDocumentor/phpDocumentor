<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Filter;

use Zend\Filter\FilterInterface;

class Filter
{
    const DEFAULT_PRIORITY = 1000;

    /** @var ClassFactory  */
    protected $factory;

    /**
     * @param ClassFactory $factory
     */
    public function __construct($factory)
    {
        $this->factory = $factory;
    }

    /**
     * Attaches a filter to a specific FQCN.
     *
     * @param string          $fqcn
     * @param FilterInterface $filter
     * @param int             $priority [1000]
     *
     * @return void
     */
    public function attach($fqcn, $filter, $priority = self::DEFAULT_PRIORITY)
    {
        $chain = $this->factory->getChainFor($fqcn);
        $chain->attach($filter, $priority);
    }

    /**
     * Filters the given Descriptor and returns the altered object.
     *
     * @param Filterable $descriptor
     *
     * @return Filterable|null
     */
    public function filter(Filterable $descriptor)
    {
        $chain = $this->factory->getChainFor(get_class($descriptor));

        return $chain->filter($descriptor);
    }
}
