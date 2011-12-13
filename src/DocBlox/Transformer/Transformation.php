<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Transformer
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Class representing a single Transformation.
 *
 * @category   DocBlox
 * @package    Transformer
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Transformer_Transformation extends DocBlox_Transformer_Abstract
{
    /** @var string */
    protected $query = '';

    /** @var DocBlox_Transformer_Writer_Abstract */
    protected $writer = null;

    /** @var string */
    protected $source = '';

    /** @var string */
    protected $artifact = '';

    /** @var string[] */
    protected $parameters = array();

    /** @var DocBlox_Transformer */
    protected $transformer = null;

    /**
     * Constructs a new Transformation object and populates the required parameters.
     *
     * @param DocBlox_Transformer $transformer  The parent transformer.
     * @param string              $query        What information to use as datasource for the writer's source.
     * @param string              $writer       What type of transformation to apply (XSLT, PDF, Checkstyle etc).
     * @param string              $source       Which template or type of source to use.
     * @param string              $artifact     What is the filename of the result (relative to the generated root)
     */
    public function __construct(DocBlox_Transformer $transformer, $query, $writer, $source, $artifact)
    {
        $this->setTransformer($transformer);
        $this->setQuery($query);
        $this->setWriter($writer);
        $this->setSource($source);
        $this->setArtifact($artifact);
    }

    /**
     * Sets the transformer object responsible for maintaining the transformations.
     *
     * @param DocBlox_Transformer $transformer
     *
     * @return void
     */
    public function setTransformer(DocBlox_Transformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Returns the transformer object which is responsible for maintaining this transformation.
     *
     * @return DocBlox_Transformer
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * Sets the query.
     *
     * @param string $query
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
     * @param string $writer
     *
     * @return void
     */
    public function setWriter($writer)
    {
        $this->writer = DocBlox_Transformer_Writer_Abstract::getInstanceOf($writer);
    }

    /**
     * Returns an instantiated writer object.
     *
     * @return DocBlox_Transformer_Writer_Abstract|null
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * Sets the source / type which the writer will use to generate artifacts from.
     *
     * @param string $source
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
     * 3. Otherwise prepend it with the DocBlox data folder, if that does
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
        if ($this->getParameter('template_path') !== null)
        {
            $path = rtrim($this->getParameter('template_path'), '/\\');
            if (file_exists($path . DIRECTORY_SEPARATOR . $this->source)) {
                return realpath($path . DIRECTORY_SEPARATOR . $this->source);
            }
        }

        if (file_exists($this->source)) {
            return realpath($this->source);
        }

        // TODO: replace this as it breaks the component stuff
        // we should ditch the idea of a global set of files to fetch and have
        // a variable / injection for the global templates folder and inject
        // that here.
        $file = dirname(__FILE__)
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . 'data'
            . DIRECTORY_SEPARATOR . $this->source;

        if (!file_exists($file))
        {
            throw new DocBlox_Transformer_Exception(
                'The source path does not exist: ' . $file
            );
        }

        return realpath($file);
    }

    /**
     * Filename of the resulting artifact relative to the root.
     *
     * If the query results in a set of artifacts (multiple nodes / array); then this string must contain an identifying
     * variable as returned by the writer.
     *
     * @param string $artifact
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
     * @param string[] $parameters
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
     * @param SimpleXMLElement $sxml
     *
     * @return (string|string[])[]
     */
    protected function convertSimpleXmlToArray(SimpleXMLElement $sxml)
    {
        $result = array();

        /** @var SimpleXMLElement $value */
        foreach ($sxml->children() as $key => $value) {
            $result[$key] = $value->count() > 1
                ? $this->convertSimpleXmlToArray($value)
                : (string)$value;
        }

        return $result;
    }

    /**
     * Imports the parameters from a SimpleXMLElement array.
     *
     * @param SimpleXMLElement $parameters
     *
     * @return void
     */
    public function importParameters(SimpleXMLElement $parameters)
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
     * @param string $name
     * @param mixed  $default
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

    public static function createFromArray(DocBlox_Transformer $transformer, array $transformation)
    {
        // check if all required items are present
        if (!key_exists('query', $transformation)
            || !key_exists('writer', $transformation)
            || !key_exists('source', $transformation)
            || !key_exists('artifact', $transformation)
        ) {
            throw new InvalidArgumentException(
                'Transformation array is missing elements, received: ' . var_export($transformation, true)
            );
        }

        $transformation_obj = new DocBlox_Transformer_Transformation(
            $transformer,
            $transformation['query'],
            $transformation['writer'],
            $transformation['source'],
            $transformation['artifact']
        );
        if (isset($transformation['parameters']) && is_array($transformation['parameters'])) {
            $transformation_obj->setParameters($transformation['parameters']);
        }

        return $transformation_obj;
    }

}