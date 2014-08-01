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

namespace phpDocumentor\Transformer\Router;

use phpDocumentor\Descriptor\DescriptorAbstract;

/**
 * Provides a queue of routers to determine the order in which they are executed.
 */
class Queue extends \SplPriorityQueue
{
    /**
     * Tries to match the given node with a rule defined in one of the routers.
     *
     * @param string|DescriptorAbstract $node
     *
     * @return Rule|null
     */
    public function match($node)
    {
        /** @var RouterAbstract $router */
        foreach (clone $this as $router) {
            $rule = $router->match($node);
            if ($rule) {
                return $rule;
            }
        }

        return null;
    }
}
