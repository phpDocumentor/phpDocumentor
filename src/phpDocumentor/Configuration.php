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

/**
 * The definition for the configuration of phpDocumentor.
 */
class Configuration
{
    /**
     * @var string
     * @Serializer\Type("string")
     */
    protected $title = '';

    /**
     * @var Parser\Configuration $parser
     * @Serializer\Type("phpDocumentor\Parser\Configuration")
     */
    protected $parser;

    /**
     * @var Configuration\Logging $logging
     * @Serializer\Type("phpDocumentor\Configuration\Logging")
     */
    protected $logging;

    /**
     * @var Transformer\Configuration $transformer
     * @Serializer\Type("phpDocumentor\Transformer\Configuration")
     */
    protected $transformer;

    /**
     * @var Parser\Configuration\Files files
     * @Serializer\Type("phpDocumentor\Parser\Configuration\Files")
     */
    protected $files;

    /**
     * @var Plugin[] $plugins
     * @Serializer\Type("array<phpDocumentor\Plugin\Plugin>")
     * @Serializer\XmlList(entry = "plugin")
     * @Merger\Replace
     */
    protected $plugins = array();

    /**
     * @var (Transformation)[]
     * @Serializer\Type("phpDocumentor\Transformer\Configuration\Transformations")
     */
    protected $transformations;

    /**
     * @var Translator\Configuration
     * @Serializer\Type("phpDocumentor\Translator\Configuration")
     */
    protected $translator;

    /**
     * @var Partial[]
     * @Serializer\Type("array<phpDocumentor\Partials\Partial>")
     */
    protected $partials = array();

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
     * @return Parser\Configuration\Files
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return Configuration\Logging
     */
    public function getLogging()
    {
        return $this->logging;
    }

    /**
     * @return Parser\Configuration
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @return Partials\Partial[]
     */
    public function getPartials()
    {
        return $this->partials;
    }

    /**
     * @return Plugin[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @return Transformer\Configuration\Transformations
     */
    public function getTransformations()
    {
        return $this->transformations;
    }

    /**
     * @return Transformer\Configuration
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * @return Translator\Configuration
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
