<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Filter;

use League\Pipeline\Pipeline;

/**
 * Retrieves a series of filters to manipulate a specific Descriptor with during building.
 */
class ClassFactory
{
    /** @var array<string, Pipeline> */
    protected $chains = [];

    /**
     * Retrieves the filters for a class with a given FQCN.
     */
    public function getChainFor(string $fqcn) : Pipeline
    {
        if (!isset($this->chains[$fqcn])) {
            $this->chains[$fqcn] = new Pipeline();
        }

        return $this->chains[$fqcn];
    }

    public function attachTo(string $fqcn, FilterInterface $filter) : void
    {
        $chain               = $this->getChainFor($fqcn);
        $this->chains[$fqcn] = $chain->pipe($filter);
    }
}
