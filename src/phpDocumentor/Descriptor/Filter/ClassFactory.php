<?php
/**
 * phpDocumentor2
 */

namespace phpDocumentor\Descriptor\Filter;

use Zend\Filter\FilterChain;

class ClassFactory
{
    protected $chains = array();

    /**
     *
     *
     * @param $fqcn
     *
     * @return FilterChain
     */
    public function getChainFor($fqcn)
    {
        if (!isset($this->chains[$fqcn])) {
            $this->chains[$fqcn] = new FilterChain();
        }

        return $this->chains[$fqcn];
    }
}
