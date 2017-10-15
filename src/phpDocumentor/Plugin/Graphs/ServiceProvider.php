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

namespace phpDocumentor\Plugin\Graphs;

use Cilex\Application;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use phpDocumentor\Plugin\Graphs\Writer\Graph;
use phpDocumentor\Transformer\Writer\Collection;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * @param Container $app An Application instance.
     */
    public function register(Container $app)
    {
        /** @var Collection $writerCollection */
        $writerCollection = $app['transformer.writer.collection'];
        $writerCollection['Graph'] = new Graph();
    }
}
