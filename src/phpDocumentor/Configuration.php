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

namespace phpDocumentor;

use JMS\Serializer\Annotation as Serializer;
use phpDocumentor\Configuration\Merger\Annotation as Merger;
use phpDocumentor\Plugin\Plugin;
use phpDocumentor\Transformer\Configuration\Transformations;

/**
 * The definition for the configuration of phpDocumentor.
 */
class Configuration
{
    /**
     * @var string The title for the generated documentation.
     * @Serializer\Type("string")
     */
    protected $title = '';

    /**
     * @var Parser\Configuration The settings used during the parsing phase.
     * @Serializer\Type("phpDocumentor\Parser\Configuration")
     */
    protected $parser;

    /**
     * @var Parser\Configuration\Files contains a list of all files and directories to parse and to ignore.
     * @Serializer\Type("phpDocumentor\Parser\Configuration\Files")
     * @deprecated to be removed in phpDocumentor 4
     */
    protected $files;

    /**
     * @var Plugin[] $plugins contains a listing of all plugins that should be loaded during startup.
     * @Serializer\Type("array<phpDocumentor\Plugin\Plugin>")
     * @Serializer\XmlList(entry = "plugin")
     * @Merger\Replace
     */
    protected $plugins = array();

    /**
     * Initializes all settings with their default values.
     */
    public function __construct()
    {
        $this->files           = new Parser\Configuration\Files();
        $this->parser          = new Parser\Configuration();
    }

    /**
     * Returns the title for the generated documentation.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Registers the title for the generated documentation.
     *
     * @param string $title
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the configuration related to which files are to be parsed.
     *
     * @deprecated to be removed in phpDocumentor 4
     * @see Parser\Configuration::setFiles() for the new location.
     *
     * @return Parser\Configuration\Files
     */
    public function getFiles()
    {
        return $this->files ?: $this->getParser()->getFiles();
    }

    /**
     * Registers the configuration related to which files are to be parsed.
     *
     * @deprecated to be removed in phpDocumentor 4
     * @see Parser\Configuration::setFiles() for the new location.
     *
     * @return Parser\Configuration\Files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * Returns the configuration used by the parser.
     *
     * @return Parser\Configuration
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Returns a list of all plugins that should be loaded by the application.
     *
     * @return Plugin[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }
}
