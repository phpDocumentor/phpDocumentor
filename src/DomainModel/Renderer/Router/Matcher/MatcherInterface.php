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

namespace phpDocumentor\DomainModel\Renderer\Router\Matcher;

/**
 * Description of the public interface to match Descriptors with a Routing rule.
 */
interface MatcherInterface
{
    /**
     * Checks whether the given string or Descriptor matches this definition.
     *
     * @param mixed $node
     *
     * @return boolean
     */
    public function __invoke($node);
}
