<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Graphs;

use phpDocumentor\Plugin\Graphs\Writer\Graph;
use phpDocumentor\Transformer\Writer\Collection;

class ServiceProvider
{
    /** @var Collection */
    private $collection;

    /** @var Graph */
    private $graphWriter;

    public function __construct(Collection $collection, Graph $graphWriter)
    {
        $this->collection = $collection;
        $this->graphWriter = $graphWriter;
    }

    /**
     * Registers services on the given app.
     */
    public function __invoke()
    {
        $this->collection['Graph'] = $this->graphWriter;
    }
}
