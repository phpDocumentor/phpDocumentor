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

namespace phpDocumentor\Parser\Configuration;

use JMS\Serializer\Annotation as Serializer;
use phpDocumentor\Configuration\Merger\Annotation as Merger;

/**
 * Represents the settings in the phpdoc.xml related to finding the files that are to be parsed.
 */
class Files
{
    /**
     * @var string[] a list of directories that contain example files
     *
     * @Serializer\Type("array<string>")
     * @Serializer\XmlList(inline = true, entry = "examples")
     */
    protected $examples = array();

    /**
     * @var string[] a list of directories that will be recursively scanned for files to parse.
     *
     * @Serializer\Type("array<string>")
     * @Serializer\XmlList(inline = true, entry = "directory")
     * @Merger\Replace
     */
    protected $directories = array();

    /**
     * @var string[] a list of files that will be parsed individually.
     *
     * @Serializer\Type("array<string>")
     * @Serializer\XmlList(inline = true, entry = "file")
     * @Merger\Replace
     */
    protected $files = array();

    /**
     * @var string[] a list of 'globs' that will determine if a file matches the expression and then will be ignored.
     *
     * @Serializer\Type("array<string>")
     * @Serializer\XmlList(inline = true, entry = "ignore")
     * @Merger\Replace
     */
    protected $ignore = array();

    /**
     * @var boolean whether to ignore hidden files and directories.
     *
     * @Serializer\Type("boolean")
     * @Serializer\SerializedName("ignore-hidden")
     */
    protected $ignoreHidden = true;

    /**
     * @var boolean whether to ignore symlinks and not follow them.
     *
     * @Serializer\Type("boolean")
     * @Serializer\SerializedName("ignore-symlinks")
     */
    protected $ignoreSymlinks = true;

    /**
     * Initializes this configuration directive with the required files and directories.
     *
     * @param string[] $directories
     * @param string[] $files
     * @param string[] $ignore
     * @param string[] $examples
     */
    public function __construct(
        array $directories = array(),
        array $files = array(),
        array $ignore = array(),
        array $examples = array()
    ) {
        $this->directories = $directories;
        $this->files       = $files;
        $this->ignore      = $ignore;
        $this->examples    = $examples;
    }

    /**
     * Returns a list of directories to recursively scan for files to be parsed.
     *
     * @return \string[]
     */
    public function getDirectories()
    {
        return $this->directories;
    }

    /**
     * Registers which directories should be scanned for files to be parsed.
     *
     * @param \string[] $directories
     *
     * @return void
     */
    public function setDirectories($directories)
    {
        $this->directories = $directories;
    }

    /**
     * Returns a list of files to parse.
     *
     * @return \string[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Registers files that are to be parsed by the parser.
     *
     * Please note that if the extension does not match what is allowed that it will be rejected nonetheless.
     *
     * @param \string[] $files
     *
     * @return void
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * Returns the 'glob' expression used to determine which files to ignore.
     *
     * @return \string[]
     */
    public function getIgnore()
    {
        return $this->ignore;
    }

    /**
     * Registers a list of files and folders (in the form of a glob) to ignore during parsing.
     *
     * @param \string[] $ignore
     *
     * @return void
     */
    public function setIgnore($ignore)
    {
        $this->ignore = $ignore;
    }

    /**
     * Returns whether to ignore hidden files and folders during parsing.
     *
     * @return boolean
     */
    public function isIgnoreHidden()
    {
        return $this->ignoreHidden;
    }

    /**
     * Registers whether to ignore hidden files and folders during parsing.
     *
     * @param boolean $ignoreHidden
     *
     * @return void
     */
    public function setIgnoreHidden($ignoreHidden)
    {
        $this->ignoreHidden = $ignoreHidden;
    }

    /**
     * Returns whether to ignore symlinks and not traverse them.
     *
     * @return boolean
     */
    public function isIgnoreSymlinks()
    {
        return $this->ignoreSymlinks;
    }

    /**
     * Registers whether to prevent traversing into paths that are symbolic links.
     *
     * @param boolean $ignoreSymlinks
     *
     * @return void
     */
    public function setIgnoreSymlinks($ignoreSymlinks)
    {
        $this->ignoreSymlinks = $ignoreSymlinks;
    }

    /**
     * Returns all folders that may contain example files as referenced using the `@example` tag.
     *
     * @return \string[]
     */
    public function getExamples()
    {
        return $this->examples;
    }

    /**
     * Registers all folders that may contain example files as referenced using the `@example` tag.
     *
     * @param \string[] $examples
     *
     * @return void
     */
    public function setExamples($examples)
    {
        $this->examples = $examples;
    }
}
