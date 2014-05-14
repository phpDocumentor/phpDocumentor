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
use phpDocumentor\Configuration\Merger\Annotation as Merger;

/**
 * Configuration definition for the parser.
 */
class Parser
{
    /**
     * @var string name of the package when there is no @package tag defined
     * @Serializer\Type("string")
     * @Serializer\SerializedName("default-package-name")
     */
    protected $defaultPackageName = 'global';

    /**
     * @var string destination location for the parser's output cache
     * @Serializer\Type("string")
     */
    protected $target;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    protected $visibility = 'public';

    /**
     * @var string default encoding of the files that are parsed
     * @Serializer\Type("string")
     */
    protected $encoding = 'utf-8';

    /**
     * @var string[] $markers
     * @Serializer\Type("array<string>")
     * @Serializer\XmlList(entry = "item")
     * @Merger\Replace
     */
    protected $markers = array();

    /**
     * @var string[] $extensions
     * @Serializer\Type("array<string>")
     * @Serializer\XmlList(entry = "extension")
     * @Merger\Replace
     */
    protected $extensions = array();

    /**
     * @return string
     */
    public function getDefaultPackageName()
    {
        return $this->defaultPackageName;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @return \string[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * @return \string[]
     */
    public function getMarkers()
    {
        return $this->markers;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }
}
