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
 * Represents the settings in the phpdoc.xml related to finding the files that are to be parsed.
 */
class Files
{
    /**
     * @var string[]
     * @Serializer\Type("array<string>")
     * @Serializer\XmlList(inline = true, entry = "directory")
     * @Merger\Replace
     */
    protected $directories;

    /**
     * @var string[]
     * @Serializer\Type("array<string>")
     * @Serializer\XmlList(inline = true, entry = "file")
     * @Merger\Replace
     */
    protected $files;

    /**
     * @var string[]
     * @Serializer\Type("array<string>")
     * @Serializer\XmlList(inline = true, entry = "ignore")
     * @Merger\Replace
     */
    protected $ignore;

    /**
     * @var boolean
     * @Serializer\Type("boolean")
     * @Serializer\SerializedName("ignore-hidden")
     */
    protected $ignoreHidden = true;

    /**
     * @var boolean
     * @Serializer\Type("boolean")
     * @Serializer\SerializedName("ignore-symlinks")
     */
    protected $ignoreSymlinks = true;

    /**
     * @return \string[]
     */
    public function getDirectories()
    {
        return $this->directories;
    }

    /**
     * @return \string[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return \string[]
     */
    public function getIgnore()
    {
        return $this->ignore;
    }

    /**
     * @return boolean
     */
    public function isIgnoreHidden()
    {
        return $this->ignoreHidden;
    }

    /**
     * @return boolean
     */
    public function isIgnoreSymlinks()
    {
        return $this->ignoreSymlinks;
    }
}
