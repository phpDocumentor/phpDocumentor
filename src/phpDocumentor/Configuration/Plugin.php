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

namespace phpDocumentor\Configuration;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("plugin")
 */
class Plugin
{
    /**
     * @Serializer\Type("string")
     * @Serializer\XmlAttribute
     * @var string
     */
    protected $path;

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
} 