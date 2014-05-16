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

namespace phpDocumentor;

use JMS\Serializer\Annotation as Serializer;
use phpDocumentor\Configuration\Logging;
use phpDocumentor\Configuration\Merger\Annotation as Merger;
use phpDocumentor\Partials\Partial;
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
     * @var Configuration\Logging The setting used for the logging of application messages and errors.
     * @Serializer\Type("phpDocumentor\Configuration\Logging")
     */
    protected $logging;

    /**
     * @var Transformer\Configuration The settings used during the transformation phase.
     * @Serializer\Type("phpDocumentor\Transformer\Configuration")
     */
    protected $transformer;

    /**
     * @var Parser\Configuration\Files contains a list of all files and directories to parse and to ignore.
     * @Serializer\Type("phpDocumentor\Parser\Configuration\Files")
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
     * @var Transformations[] contains a list of all templates and custom transformations that are to be executed during
     *     the transformation process.
     * @Serializer\Type("phpDocumentor\Transformer\Configuration\Transformations")
     */
    protected $transformations;

    /**
     * @var Translator\Configuration The settings used during translation.
     * @Serializer\Type("phpDocumentor\Translator\Configuration")
     */
    protected $translator;

    /**
     * @var Partial[] A list of custom texts, or references thereto, that may be injected into templates.
     * @Serializer\Type("array<phpDocumentor\Partials\Partial>")
     */
    protected $partials = array();

    /**
     * Initializes all settings with their default values.
     */
    public function __construct()
    {
        $this->transformer     = new Transformer\Configuration();
        $this->transformations = new Transformer\Configuration\Transformations();
        $this->files           = new Parser\Configuration\Files();
        $this->parser          = new Parser\Configuration();
        $this->logging         = new Logging();
        $this->translator      = new Translator\Configuration();
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
     * Returns the configuration related to which files are to be parsed.
     *
     * @return Parser\Configuration\Files
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Returns the settings related to logging.
     *
     * @return Configuration\Logging
     */
    public function getLogging()
    {
        return $this->logging;
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
     * Returns all partials that can be imported in the application.
     *
     * @return Partials\Partial[]
     */
    public function getPartials()
    {
        return $this->partials;
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

    /**
     * Returns which templates and custom transformations need to be applied to the parsed data.
     *
     * @return Transformer\Configuration\Transformations
     */
    public function getTransformations()
    {
        return $this->transformations;
    }

    /**
     * Returns the settings for the transformer.
     *
     * @return Transformer\Configuration
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * Returns the settings for the translator.
     *
     * @return Translator\Configuration
     */
    public function getTranslator()
    {
        return $this->translator;
    }
}
