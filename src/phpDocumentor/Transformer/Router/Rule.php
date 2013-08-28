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

namespace phpDocumentor\Transformer\Router;

/**
 * A rule determines if and how a node should be transformed to an URL.
 */
use phpDocumentor\Descriptor\DescriptorAbstract;

class Rule
{
    protected $matcher;
    protected $generator;

    /**
     * @param callable $matcher
     * @param callable $generator
     */
    public function __construct($matcher, $generator)
    {
        $this->matcher   = $matcher;
        $this->generator = $generator;
    }

    /**
     * @param string|DescriptorAbstract $node
     *
     * @return mixed
     */
    public function match($node)
    {
        $callable = $this->matcher;

        return $callable($node);
    }

    /**
     * Generates an URL for the given node.
     *
     * @param string|DescriptorAbstract $node The node for which to generate an URL.
     *
     * @return string|false a well-formed relative or absolute URL, or false if no URL could be generated.
     */
    public function generate($node)
    {
        $callable = $this->generator;

        return $callable($node);
    }
}
