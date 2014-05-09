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

/**
 * A rule determines if and how a node should be transformed to an URL.
 */
use phpDocumentor\Descriptor\DescriptorAbstract;

class Rule
{
    /** @var callable */
    protected $generator;

    /** @var callable */
    protected $matcher;

    /**
     * Initializes this rule.
     *
     * @param callable $matcher   A closure that returns a boolean indicating whether this rule applies to the
     *     provided node.
     * @param callable $generator A closure that returns a url or null for the given node.
     */
    public function __construct($matcher, $generator)
    {
        $this->matcher   = $matcher;
        $this->generator = $generator;
    }

    /**
     * Returns true when this rule is applicable to the given node.
     *
     * @param string|DescriptorAbstract $node
     *
     * @return boolean
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
