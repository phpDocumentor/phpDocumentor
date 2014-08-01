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

namespace phpDocumentor\Transformer\Router\Matcher;

use phpDocumentor\Descriptor\DescriptorAbstract;

/**
 * Description of the public interface to match Descriptors with a Routing rule.
 */
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
