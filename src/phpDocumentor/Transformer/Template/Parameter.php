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

namespace phpDocumentor\Transformer\Template;

use JMS\Serializer\Annotation as Serializer;

/**
 * Model representing a parameter in a template or transformation.
 *
 * @Serializer\XmlRoot("parameter")
 */
class Parameter
{
    /**
     * @Serializer\Type("string")
     * @var string
     * @Serializer\XmlAttribute
     */
    protected $key;

    /**
     * @Serializer\Type("string")
     * @var string
     * @Serializer\XmlValue
     */
    protected $value;

    /**
     * Sets an XML attribute
     *
     * @param string $key
     * @return $this for a fluent interface
     */
    public function setKey($key)
    {
        $this->key =  $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Sets an XML value
     *
     * @param string $value
     * @return $this for a fluent interface
     */
    public function setValue($value)
    {
        $this->value =  $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
