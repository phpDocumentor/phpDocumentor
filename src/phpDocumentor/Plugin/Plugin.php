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

namespace phpDocumentor\Plugin;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("plugin")
 */
class Plugin
{
    /**
     * @var string class name of the plugin.
     *
     * @todo this serialized name is misleading, the (old) docs should be reviewed for accuracy
     *
     * @Serializer\Type("string")
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("path")
     */
    protected $className;

    /**
     * @Serializer\XmlList(entry = "parameter")
     * @Serializer\Type("array<phpDocumentor\Plugin\Parameter>")
     * @var Parameter[] parameters that are configured by the user
     */
    protected $parameters = array();

    /**
     * Initialize the plugin configuration definition with the given class name.
     *
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * Returns the class name for this plugin.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Returns the parameters associated with this plugin.
     *
     * @return Parameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
