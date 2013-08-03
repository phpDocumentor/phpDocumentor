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

use Psr\Log\LogLevel;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Event\DebugEvent;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Event\LogEvent;
use phpDocumentor\Transformer\Event\PostTransformEvent;
use phpDocumentor\Transformer\Event\PostTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Event\PreTransformationEvent;

/**
 * Core class responsible for transforming the cache file to a set of artifacts.
 */
class Transformer implements CompilerPassInterface
{
    /** @var integer represents the priority in the Compiler queue. */
    const COMPILER_PRIORITY = 5000;

    /** @var string|null $target Target location where to output the artifacts */
    protected $target = null;

    /** @var Template\Collection $templates */
    protected $templates;

    /** @var Writer\Collection $writers */
    protected $writers;

    /** @var Behaviour\Collection|null $behaviours */
    protected $behaviours = null;

    /** @var Transformation[] $transformations */
    protected $transformations = array();

    /**
     * Wires the template collection and writer collection to this transformer.
     *
     * @param Template\Collection $templateCollection
     * @param Writer\Collection   $writerCollection
     */
    public function __construct(Template\Collection $templateCollection, Writer\Collection $writerCollection)
    {
        $this->templates = $templateCollection;
        $this->writers   = $writerCollection;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return 'Transform analyzed project into artifacts';
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
    protected $externalClassDocs = array();

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
        if (false === $path) {
            if (mkdir($target, 0755, true)) {
                $path = realpath($target);
            } else {
                throw new \InvalidArgumentException(
                    'Target directory ('
                    . $target .
                    ') does not exist and could not be created'
                );
            }
        }

        if (!is_dir($path) || !is_writable($path)) {
            throw new \InvalidArgumentException(
                'Given target (' . $target . ') is not a writeable directory'
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
     * Returns the list of templates which are going to be adopted.
     *
     * @return Template\Collection
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * Transforms the given project into a series of artifacts as provided by the templates.
     *
     * @param ProjectDescriptor $project
     *
     * @return void
     */
    public function execute(ProjectDescriptor $project)
    {
        Dispatcher::getInstance()->dispatch('transformer.transform.pre', PreTransformEvent::createInstance($this));

        if ($this->getBehaviours() instanceof Behaviour\Collection) {
            $this->log(sprintf('Applying %d behaviours', count($this->getBehaviours())));
            $this->getBehaviours()->process($project);
        }

        $transformations = $this->getTemplates()->getTransformations();
        $this->log(sprintf('Applying %d transformations', count($transformations)));
        foreach ($transformations as $transformation) {
            $this->log(
                '  Writer ' . $transformation->getWriter()
                . ($transformation->getQuery() ? (' using query "' . $transformation->getQuery() . '"') : '')
                . ' on '.$transformation->getArtifact()
            );

            $transformation->setTransformer($this);

            /** @var Writer\WriterAbstract $writer  */
            $writer = $this->writers[$transformation->getWriter()];

            Dispatcher::getInstance()->dispatch(
                'transformer.transformation.pre',
                PreTransformationEvent::createInstance($this)
            );
            $writer->transform($project, $transformation);
            Dispatcher::getInstance()->dispatch(
                'transformer.transformation.post',
                PostTransformationEvent::createInstance($this)
            );
        }

        Dispatcher::getInstance()->dispatch('transformer.transform.post', PostTransformEvent::createInstance($this));

        $this->log('Finished transformation process');
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
     * @codeCoverageIgnore
     * @deprecated should be moved to the new router
     *
     * @return void
     */
    public function setExternalClassDoc($prefix, $uri)
    {
        $this->externalClassDocs[$prefix] = $uri;
    }

    /**
     * Sets a set of prefix -> url parts.
     *
     * @param string[] $external_class_docs Array containing prefix => URI pairs.
     *
     * @see self::setExternalClassDoc() for details on this feature.
     *
     * @codeCoverageIgnore
     * @deprecated should be moved to the new router
     *
     * @return void
     */
    public function setExternalClassDocs($external_class_docs)
    {
        $this->externalClassDocs = $external_class_docs;
    }

    /**
     * Returns the registered prefix -> url pairs.
     *
     * @codeCoverageIgnore
     * @deprecated should be moved to the new router
     *
     * @return string[]
     */
    public function getExternalClassDocs()
    {
        return $this->externalClassDocs;
    }

    /**
     * Retrieves the url for a given prefix.
     *
     * @param string $prefix Class prefix to retrieve a URL for.
     * @param string $class  If provided will replace the {CLASS} param with
     *  this string.
     *
     * @codeCoverageIgnore
     * @deprecated should be moved to the new router
     *
     * @return string|null
     */
    public function getExternalClassDocumentLocation($prefix, $class = null)
    {
        if (!isset($this->externalClassDocs[$prefix])) {
            return null;
        }

        $result = $this->externalClassDocs[$prefix];
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
     * @codeCoverageIgnore
     * @deprecated should be moved to the new router
     *
     * @return null|string
     */
    public function findExternalClassDocumentLocation($class)
    {
        $class = ltrim($class, '\\');
        foreach (array_keys($this->externalClassDocs) as $prefix) {
            if (strpos($class, $prefix) === 0) {
                return $this->getExternalClassDocumentLocation($prefix, $class);
            }
        }

        return null;
    }

    /**
     * Dispatches a logging request.
     *
     * @param string $message  The message to log.
     * @param int    $priority The logging priority
     *
     * @return void
     */
    public function log($message, $priority = LogLevel::INFO)
    {
        Dispatcher::getInstance()->dispatch(
            'system.log',
            LogEvent::createInstance($this)
                ->setMessage($message)
                ->setPriority($priority)
        );
    }

    /**
     * Dispatches a logging request to log a debug message.
     *
     * @param string $message The message to log.
     *
     * @return void
     */
    public function debug($message)
    {
        Dispatcher::getInstance()->dispatch(
            'system.debug',
            DebugEvent::createInstance($this)
                ->setMessage($message)
        );
    }
}
