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

namespace phpDocumentor\Transformer\Writer;

use phpDocumentor\Transformer\Router\Queue;

interface Routable
{
    /**
     * Sets the routers that can be used to determine the path of links.
     *
     * @param Queue $routers
     *
     * @return void
     */
    public function setRouters(Queue $routers);
}
