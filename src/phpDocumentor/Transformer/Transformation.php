<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Transformer
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer;

/**
 * Class representing a single Transformation.
 *
 * @category phpDocumentor
 * @package  Transformer
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class Transformation extends TransformerAbstract
{
    /** @var string */
    protected $query = '';

    /** @var Writer\WriterAbstract */
    protected $writer = null;

    /** @var string */
    protected $source = '';

    /** @var string */
    protected $artifact = '';

    /** @var string[] */
    protected $parameters = array();

    /** @var Transformer */
    protected $transformer = null;

    /**
     * Constructs a new Transformation object and populates the required parameters.
     *
     * @param Transformer $transformer The parent transformer.
     * @param string      $query       What information to use as datasource for
     *     the writer's source.
     * @param string      $writer      What type of transformation to apply
     *     (XSLT, PDF, Checkstyle etc).
     * @param string      $source      Which template or type of source to use.
     * @param string      $artifact    What is the filename of the result
     *     (relative to the generated root)
     */
    public function __construct(
        Transformer $transformer, $query, $writer, $source, $artifact
    ) {
        $this->setTransformer($transformer);
        $this->setQuery($query);
        $this->setWriter($writer);
        $this->setSource($source);
        $this->setArtifact($artifact);
    }

    /**
     * Sets the transformer object responsible for maintaining the transformations.
     *
     * @param Transformer $transformer Responsible transformer object.
     *
     * @return void
     */
    public function setTransformer(Transformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Returns the transformer object which is responsible for maintaining this
     * transformation.
     *
     * @return Transformer
     */
    public function getTransformer()
    {
        return $this->transformer;
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
        $this->writer = Writer\WriterAbstract::getInstanceOf($writer);
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
            $path = rtrim($this->getParameter('template_path'), '/\\');
            if (file_exists($path . DIRECTORY_SEPARATOR . $this->source)) {
                return $path . DIRECTORY_SEPARATOR . $this->source;
            }
        }

        // check whether the file exists in the phpDocumentor project directory
        if (file_exists(__DIR__.'/../../../'.$this->source)) {
            return __DIR__ . '/../../../' .$this->source;
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
     * @param string[] $parameters Associative multidimensional array containing
     *     parameters for the Writer.
     *
     * @return void
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Recursive function to convert a SimpleXMLElement to an associative array.
     *
     * @param \SimpleXMLElement $sxml object to convert to a flat array.
     *
     * @return (string|string[])[]
     */
    protected function convertSimpleXmlToArray(\SimpleXMLElement $sxml)
    {
        $result = array();

        /** @var \SimpleXMLElement $value */
        foreach ($sxml->children() as $key => $value) {
            $result[$key] = count($value->children()) > 1
                ? $this->convertSimpleXmlToArray($value)
                : (string)$value;
        }

        return $result;
    }

    /**
     * Imports the parameters from a SimpleXMLElement array.
     *
     * @param \SimpleXMLElement $parameters Object to import
     *
     * @return void
     */
    public function importParameters(\SimpleXMLElement $parameters)
    {
        $this->parameters = $this->convertSimpleXmlToArray($parameters);
    }

    /**
     * Returns all parameters for this transformation.
     *
     * @return string[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns a specific parameter, or $default if none exists.
     *
     * @param string $name    Name of the parameter to return.
     * @param mixed  $default Default value is parameter does not exist.
     *
     * @return string
     */
    public function getParameter($name, $default = null)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }

    /**
     * Executes the transformation.
     *
     * @param string $structure_file The location of the structure file.
     *
     * @return void
     */
    public function execute($structure_file)
    {
        $this->getWriter()->transform($structure_file, $this);
    }

    /**
     * Factory method to create a new transformation based on a array containing
     * transformation parameters.
     *
     * The array format is:
     *
     * <code>
     * array(
     *   'query'      => 'Query string',
     *   'writer'     => 'WriterName',
     *   'source'     => 'source location',
     *   'artifact'   => 'path',
     *   'parameters' => array()
     * )
     * </code>
     *
     * The parameter array is optional.
     *
     * @param Transformer $transformer    Responsible transformer object.
     * @param mixed[]     $transformation Transformation array, see long
     *     description for the format.
     *
     * @throws \InvalidArgumentException
     *
     * @return Transformation
     */
    public static function createFromArray(
        Transformer $transformer, array $transformation
    ) {
        // check if all required items are present
        if (!array_key_exists('query', $transformation)
            || !array_key_exists('writer', $transformation)
            || !array_key_exists('source', $transformation)
            || !array_key_exists('artifact', $transformation)
        ) {
            throw new \InvalidArgumentException(
                'Transformation array is missing elements, received: '
                . var_export($transformation, true)
            );
        }

        $transformation_obj = new Transformation(
            $transformer,
            $transformation['query'],
            $transformation['writer'],
            $transformation['source'],
            $transformation['artifact']
        );
        if (isset($transformation['parameters'])
            && is_array($transformation['parameters'])
        ) {
            $transformation_obj->setParameters($transformation['parameters']);
        }

        return $transformation_obj;
    }

}