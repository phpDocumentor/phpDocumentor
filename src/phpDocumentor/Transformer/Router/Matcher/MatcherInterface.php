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

namespace phpDocumentor\Transformer\Router\Matcher;

use phpDocumentor\Descriptor\DescriptorAbstract;

interface MatcherInterface
{
    /**
     * Checks whether the given string or Descriptor matches this definition.
     *
     * @param string|DescriptorAbstract $node
     *
     * @return boolean
     */
    public function __invoke($node);
}
