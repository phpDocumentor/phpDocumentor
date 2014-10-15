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

namespace phpDocumentor\Transformer\Configuration;

use JMS\Serializer\Annotation as Serializer;

/**
 * Reference that relates classes with a specific prefix to a URL template.
 */
class ExternalClassDocumentation
{
    /**
     * @var string the prefix that a class should have to contain in order for this reference to apply.
     *
     * @Serializer\Type("string")
     */
    protected $prefix;

    /**
     * @var string The URI template that is used to construct a link to the documentation.
     *
     * @Serializer\Type("string")
     */
    protected $uri;

    /**
     * Registers the prefix and uri on this configuration item.
     *
     * @param string $prefix
     * @param string $uri
     */
    public function __construct($prefix, $uri)
    {
        $this->prefix = $prefix;
        $this->uri    = $uri;
    }

    /**
     * Returns the prefix that a class should have to contain in order for this reference to apply.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * The URI template that is used to construct a link to the documentation for classes with the prefix in this
     * reference.
     *
     * The URI templates may contain the following variables:
     *
     * - {CLASS}, the Qualified Class Name for the discovered class will be inserted here.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }
}
