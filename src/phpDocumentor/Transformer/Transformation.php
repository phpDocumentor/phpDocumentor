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

namespace phpDocumentor\Transformer;

use JMS\Serializer\Annotation as Serializer;
use phpDocumentor\Transformer\Template\Parameter;

/**
 * Class representing a single Transformation.
 */
class Transformation
{
    /**
     * @Serializer\XmlAttribute
     * @Serializer\Type("string")
     * @var string Reference to an object containing the business logic used to execute this transformation.
     */
    protected $writer = null;

    /**
     * @Serializer\XmlAttribute
     * @Serializer\Type("string")
     * @var string the location where the output should be sent to; the exact function differs per writer.
     */
    protected $artifact = '';

    /**
     * @Serializer\XmlAttribute
     * @Serializer\Type("string")
     * @var string the location where input for a writer should come from; the exact function differs per writer.
     */
    protected $source = '';

    /**
     * @Serializer\XmlAttribute
     * @Serializer\Type("string")
     * @var string a filter or other form of limitation on what information of the AST is used; the exact function
     *     differs per writer.
     */
    protected $query = '';

    /**
     * @Serializer\Exclude
     * @var Transformer $transformer The object guiding the transformation process and having meta-data of it.
     */
    protected $transformer;

    /**
     * @Serializer\XmlList(entry = "parameter")
     * @Serializer\Type("array<phpDocumentor\Transformer\Template\Parameter>")
     * @var Parameter[] A series of parameters that can influence what the writer does; the exact function differs
     *     per writer.
     */
    protected $parameters = array();

    /**
     * Constructs a new Transformation object and populates the required parameters.
     *
     * @param string $query       What information to use as datasource for the writer's source.
     * @param string $writer      What type of transformation to apply (XSLT, PDF, Checkstyle etc).
     * @param string $source      Which template or type of source to use.
     * @param string $artifact    What is the filename of the result (relative to the generated root)
     */
    public function __construct($query, $writer, $source, $artifact)
    {
        $this->setQuery($query);
        $this->setWriter($writer);
        $this->setSource($source);
        $this->setArtifact($artifact);
    }

    /**
     * Sets the query.
     *
     * @param string $query Free-form string with writer-specific values.
     *
     * @return void
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * Returns the set query.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Sets the writer type and instantiates a writer.
     *
     * @param string $writer Name of writer to instantiate.
     *
     * @return void
     */
    public function setWriter($writer)
    {
        $this->writer = $writer;
    }

    /**
     * Returns an instantiated writer object.
     *
     * @return Writer\WriterAbstract|null
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * Sets the source / type which the writer will use to generate artifacts from.
     *
     * @param string $source Free-form string with writer-specific meaning.
     *
     * @return void
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * Returns the name of the source / type used in the transformation process.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Returns the source as a path instead of a regular value.
     *
     * This method applies the following rules to the value of $source:
     *
     * 1. if the template_path parameter is set and that combined with the
     *    source gives an existing file; return that.
     * 2. if the value exists as a file (either relative to the current working
     *    directory or absolute), do a realpath and return it.
     * 3. Otherwise prepend it with the phpDocumentor data folder, if that does
     *    not exist: throw an exception
     *
     * @throws Exception if no valid file could be found.
     *
     * @return string
     */
    public function getSourceAsPath()
    {
        // externally loaded templates set this parameter so that template
        // resources may be placed in the same folder as the template.
        if ($this->getParameter('template_path') !== null) {
            $path = rtrim($this->getParameter('template_path')->getValue(), '/\\');
            if (file_exists($path . DIRECTORY_SEPARATOR . $this->source)) {
                return $path . DIRECTORY_SEPARATOR . $this->source;
            }
        }

        // counter a BC break that we introduced in 2.0 stable; we removed the notion of global assets
        // to be able to provide composer integration
        // TODO: remove in version 3.0
        if (strpos($this->source, 'templates/') !== 0) {
            $this->source = 'templates/abstract/' . $this->source;
            trigger_error(
                'Using shared assets in a template is deprecated and will be removed in version 3.0',
                E_USER_DEPRECATED
            );
        }

        // check whether the file exists in the phpDocumentor project directory.
        if (file_exists(__DIR__.'/../../../'.$this->source)) {
            return __DIR__ . '/../../../' .$this->source;
        }

        // in case of a composer installation
        if (file_exists(__DIR__ . '/../../../../templates')) {
            return __DIR__ . '/../../../../' . $this->source;
        }

        // TODO: replace this as it breaks the component stuff
        // we should ditch the idea of a global set of files to fetch and have
        // a variable / injection for the global templates folder and inject
        // that here.
        $file = __DIR__.'/../../../data/'.$this->source;

        if (!file_exists($file)) {
            throw new Exception('The source path does not exist: ' . $file);
        }

        return $file;
    }

    /**
     * Filename of the resulting artifact relative to the root.
     *
     * If the query results in a set of artifacts (multiple nodes / array);
     * then this string must contain an identifying variable as returned by the
     * writer.
     *
     * @param string $artifact Name of artifact to generate; usually a filepath.
     *
     * @return void
     */
    public function setArtifact($artifact)
    {
        $this->artifact = $artifact;
    }

    /**
     * Returns the name of the artifact.
     *
     * @return string
     */
    public function getArtifact()
    {
        return $this->artifact;
    }

    /**
     * Sets an array of parameters (key => value).
     *
     * @param Parameter[] $parameters Associative multidimensional array containing
     *     parameters for the Writer.
     *
     * @return void
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns all parameters for this transformation.
     *
     * @return Parameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns a specific parameter, or $default if none exists.
     *
     * @param string $name Name of the parameter to return.
     *
     * @return Parameter
     */
    public function getParameter($name)
    {
        /** @var Parameter $parameter */
        foreach ($this->parameters as $parameter) {
            if ($parameter->getKey() == $name) {
                return $parameter;
            }
        }

        return null;
    }

    /**
     * Returns a specific parameter, or $default if none exists.
     *
     * @param string $name Name of the parameter to return.
     *
     * @return Parameter
     */
    public function getParametersWithKey($name)
    {
        $parameters = array();

        /** @var Parameter $parameter */
        foreach ($this->parameters as $parameter) {
            if ($parameter->getKey() == $name) {
                $parameters[] = $parameter;
            }
        }

        return $parameters;
    }

    /**
     * Sets the transformer on this transformation.
     *
     * @param \phpDocumentor\Transformer\Transformer $transformer
     */
    public function setTransformer($transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Returns the transformer for this transformation.
     *
     * @return \phpDocumentor\Transformer\Transformer
     */
    public function getTransformer()
    {
        return $this->transformer;
    }
}
