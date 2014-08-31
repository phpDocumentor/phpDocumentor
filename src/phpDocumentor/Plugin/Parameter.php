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
 * Model representing a plugin parameter
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
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
