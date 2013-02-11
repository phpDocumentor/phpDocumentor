<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer;

use phpDocumentor\Descriptor\ProjectDescriptor;

/**
 * Core class responsible for transforming the cache file to a set of artifacts.
 */
class Transformer extends TransformerAbstract
{
    /** @var string|null $target Target location where to output the artifacts */
    protected $target = null;

    /** @var \phpDocumentor\Transformer\Template[] $templates */
    protected $templates = array();

    /** @var string $templates_path */
    protected $templates_path = '';

    /** @var Behaviour\Collection|null $behaviours */
    protected $behaviours = null;

    /** @var Template\Factory $templateFactory */
    protected $templateFactory = null;

    /** @var Transformation[] $transformations */
    protected $transformations = array();

    /** @var boolean $parsePrivate */
    protected $parsePrivate = false;

    /**
     * Wires the template factory to this transformer.
     *
     * @param Template\Factory $templateFactory
     */
    public function __construct(Template\Factory $templateFactory)
    {
        $this->templateFactory = $templateFactory;
    }

    /**
     * Array containing prefix => URL values.
     *
     * What happens is that the transformer knows where to find external API
     * docs for classes with a certain prefix.
     *
     * For example: having a prefix HTML_QuickForm2_ will link an unidentified
     * class that starts with HTML_QuickForm2_ to a (defined) URL
     * i.e. http://pear.php.net/package/HTML_QuickForm2/docs/
     * latest/HTML_QuickForm2/${class}.html
     *
     * @var string
     */
    protected $external_class_docs = array();

    /**
     * Sets the collection of behaviours that are applied before the actual transformation process.
     *
     * @param Behaviour\Collection $behaviours
     *
     * @return void
     */
    public function setBehaviours(Behaviour\Collection $behaviours)
    {
        $this->behaviours = $behaviours;
    }

    /**
     * Retrieves the collection of behaviours that should occur before the transformation process.
     *
     * @return Behaviour\Collection|null
     */
    public function getBehaviours()
    {
        return $this->behaviours;
    }

    /**
     * Sets the target location where to output the artifacts.
     *
     * @param string $target The target location where to output the artifacts.
     *
     * @throws \InvalidArgumentException if the target is not a valid writable
     *   directory.
     *
     * @return void
     */
    public function setTarget($target)
    {
        $path = realpath($target);
        if (!file_exists($path) && !is_dir($path) && !is_writable($path)) {
            throw new \InvalidArgumentException(
                'Given target directory (' . $target . ') does not exist or '
                . 'is not writable'
            );
        }

        $this->target = $path;
    }

    /**
     * Returns the location where to store the artifacts.
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Sets the path where the templates are located.
     *
     * @param string $path Absolute path where the templates are.
     *
     * @return void
     */
    public function setTemplatesPath($path)
    {
        $this->templates_path = $path;
    }

    /**
     * Returns the path where the templates are located.
     *
     * @return string
     */
    public function getTemplatesPath()
    {
        return $this->templates_path;
    }

    /**
     * Sets flag indicating whether private members and/or elements tagged
     * as {@internal} need to be displayed.
     *
     * @param bool $val True if all needs to be shown, false otherwise.
     *
     * @return void
     */
    public function setParseprivate($val)
    {
        $this->parsePrivate = (boolean)$val;
    }

    /**
     * Returns flag indicating whether private members and/or elements tagged
     * as {@internal} need to be displayed.
     *
     * @return bool
     */
    public function getParseprivate()
    {
        return $this->parsePrivate;
    }

    /**
     * Sets one or more templates as basis for the transformations.
     *
     * @param string|string[] $template Name or names of the templates.
     *
     * @return void
     */
    public function setTemplates($template)
    {
        $this->templates = array();

        if (!is_array($template)) {
            $template = array($template);
        }

        foreach ($template as $item) {
            $this->addTemplate($item);
        }
    }

    /**
     * Returns the list of templates which are going to be adopted.
     *
     * @return string[]
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * Loads a template by name, if an additional array with details is provided it will try to load parameters from it.
     *
     * @param string $name Name of the template to add.
     *
     * @return void
     */
    public function addTemplate($name)
    {
        // if the template is already loaded we do not reload it.
        if (isset($this->templates[$name])) {
            return;
        }

        $this->templates[$name] = $this->getTemplateFactory()->create($name, $this);
    }

    /**
     * Returns the transformation which this transformer will process.
     *
     * @return Transformation[]
     */
    public function getTransformations()
    {
        $result = array();
        foreach ($this->templates as $template) {
            foreach ($template as $transformation) {
                $result[] = $transformation;
            }
        }

        return $result;
    }

    /**
     * Executes each transformation.
     *
     * @param ProjectDescriptor $project
     *
     * @return void
     */
    public function execute(ProjectDescriptor $project)
    {
        if ($this->getBehaviours() instanceof Behaviour\Collection) {
            $this->getBehaviours()->process($project);
        }

        foreach ($this->getTransformations() as $transformation) {
            $this->log(
                'Applying transformation'
                . ($transformation->getQuery() ? (' query "' . $transformation->getQuery() . '"') : '')
                . ' using writer ' . get_class($transformation->getWriter())
                . ' on '.$transformation->getArtifact()
            );

            $transformation->execute($project);
        }
    }

    /**
     * Converts a source file name to the name used for generating the end result.
     *
     * This method strips down the given $name using the following rules:
     *
     * * if the $name is postfixed with .php then that is removed
     * * any occurance of \ or DIRECTORY_SEPARATOR is replaced with .
     * * any dots that the name starts or ends with is removed
     * * the result is postfixed with .html
     *
     * @param string $name Name to convert.
     *
     * @return string
     */
    public function generateFilename($name)
    {
        if (substr($name, -4) == '.php') {
            $name = substr($name, 0, -4);
        }

        return trim(str_replace(array(DIRECTORY_SEPARATOR, '\\'), '.', trim($name, DIRECTORY_SEPARATOR . '.')), '.')
            . '.html';
    }

    /**
     * Adds a link to external documentation.
     *
     * Please note that the prefix string is matched against the
     * start of the class name and that the preceding \ for namespaces
     * should NOT be included.
     *
     * You can augment the URI with the name of the found class by inserting
     * the param {CLASS}. By default the class is inserted as-is; to insert a
     * lowercase variant use the parameter {LOWERCASE_CLASS}
     *
     * @param string $prefix Class prefix to match, i.e. Zend_Config_
     * @param string $uri    URI to link to when above prefix is encountered.
     *
     * @return void
     */
    public function setExternalClassDoc($prefix, $uri)
    {
        $this->external_class_docs[$prefix] = $uri;
    }

    /**
     * Sets a set of prefix -> url parts.
     *
     * @param string[] $external_class_docs Array containing prefix => URI pairs.
     *
     * @see self::setExternalClassDoc() for details on this feature.
     *
     * @return void
     */
    public function setExternalClassDocs($external_class_docs)
    {
        $this->external_class_docs = $external_class_docs;
    }

    /**
     * Returns the registered prefix -> url pairs.
     *
     * @return string[]
     */
    public function getExternalClassDocs()
    {
        return $this->external_class_docs;
    }

    /**
     * Retrieves the url for a given prefix.
     *
     * @param string $prefix Class prefix to retrieve a URL for.
     * @param string $class  If provided will replace the {CLASS} param with
     *  this string.
     *
     * @return string|null
     */
    public function getExternalClassDocumentLocation($prefix, $class = null)
    {
        if (!isset($this->external_class_docs[$prefix])) {
            return null;
        }

        $result = $this->external_class_docs[$prefix];
        if ($class !== null) {
            $result = str_replace(
                array('{CLASS}', '{LOWERCASE_CLASS}', '{UNPREFIXED_CLASS}'),
                array($class, strtolower($class), substr($class, strlen($prefix))),
                $result
            );
        }

        return $result;
    }

    /**
     * Returns the url for this class if it is registered.
     *
     * @param string $class FQCN to retrieve documentation URL for.
     *
     * @return null|string
     */
    public function findExternalClassDocumentLocation($class)
    {
        $class = ltrim($class, '\\');
        foreach (array_keys($this->external_class_docs) as $prefix) {
            if (strpos($class, $prefix) === 0) {
                return $this->getExternalClassDocumentLocation($prefix, $class);
            }
        }

        return null;
    }

    /**
     * Returns the template factory.
     *
     * @return Template\Factory
     */
    public function getTemplateFactory()
    {
        return $this->templateFactory;
    }
}
