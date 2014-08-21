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

use phpDocumentor\Transformer\Event\WriterInitializationEvent;
use phpDocumentor\Transformer\Writer\Initializable;
use phpDocumentor\Transformer\Writer\WriterAbstract;
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
    const EVENT_PRE_TRANSFORMATION = 'transformer.transformation.pre';
    const EVENT_POST_TRANSFORMATION = 'transformer.transformation.post';
    const EVENT_PRE_INITIALIZATION = 'transformer.writer.initialization.pre';
    const EVENT_POST_INITIALIZATION = 'transformer.writer.initialization.post';
    const EVENT_PRE_TRANSFORM = 'transformer.transform.pre';
    const EVENT_POST_TRANSFORM = 'transformer.transform.post';

    /** @var integer represents the priority in the Compiler queue. */
    const COMPILER_PRIORITY = 5000;

    /** @var string|null $target Target location where to output the artifacts */
    protected $target = null;

    /** @var Template\Collection $templates */
    protected $templates;

    /** @var Writer\Collection|WriterAbstract[] $writers */
    protected $writers;

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
            if (@mkdir($target, 0755, true)) {
                $path = realpath($target);
            } else {
                throw new \InvalidArgumentException(
                    'Target directory (' . $target . ') does not exist and could not be created'
                );
            }
        }

        if (!is_dir($path) || !is_writable($path)) {
            throw new \InvalidArgumentException('Given target (' . $target . ') is not a writable directory');
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
        Dispatcher::getInstance()->dispatch(
            self::EVENT_PRE_TRANSFORM,
            PreTransformEvent::createInstance($this)->setProject($project)
        );

        $transformations = $this->getTemplates()->getTransformations();
        $this->initializeWriters($project, $transformations);
        $this->transformProject($project, $transformations);

        Dispatcher::getInstance()->dispatch(self::EVENT_POST_TRANSFORM, PostTransformEvent::createInstance($this));

        $this->log('Finished transformation process');
    }

    /**
     * Converts a source file name to the name used for generating the end result.
     *
     * This method strips down the given $name using the following rules:
     *
     * * if the $name is suffixed with .php then that is removed
     * * any occurrence of \ or DIRECTORY_SEPARATOR is replaced with .
     * * any dots that the name starts or ends with is removed
     * * the result is suffixed with .html
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
     * Dispatches a logging request.
     *
     * @param string $message  The message to log.
     * @param string $priority The logging priority
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

    /**
     * Initializes all writers that are used during this transformation.
     *
     * @param ProjectDescriptor $project
     * @param Transformation[]  $transformations
     *
     * @return void
     */
    private function initializeWriters(ProjectDescriptor $project, $transformations)
    {
        $isInitialized = array();
        foreach ($transformations as $transformation) {
            $writerName = $transformation->getWriter();

            if (in_array($writerName, $isInitialized)) {
                continue;
            }

            $isInitialized[] = $writerName;
            $writer = $this->writers[$writerName];
            $this->initializeWriter($writer, $project);
        }
    }

    /**
     * Initializes the given writer using the provided project meta-data.
     *
     * This method wil call for the initialization of each writer that supports an initialization routine (as defined by
     * the `Initializable` interface).
     *
     * In addition to this, the following events emitted for each writer that is present in the collected list of
     * transformations, even those that do not implement the `Initializable` interface.
     *
     * Emitted events:
     *
     * - transformer.writer.initialization.pre, before the initialization of a single writer.
     * - transformer.writer.initialization.post, after the initialization of a single writer.
     *
     * @param WriterAbstract    $writer
     * @param ProjectDescriptor $project
     *
     * @uses Dispatcher to emit the events surrounding an initialization.
     *
     * @return void
     */
    private function initializeWriter(WriterAbstract $writer, ProjectDescriptor $project)
    {
        $event = WriterInitializationEvent::createInstance($this)->setWriter($writer);
        Dispatcher::getInstance()->dispatch(self::EVENT_PRE_INITIALIZATION, $event);

        if ($writer instanceof Initializable) {
            $writer->initialize($project);
        }

        Dispatcher::getInstance()->dispatch(self::EVENT_POST_INITIALIZATION, $event);
    }

    /**
     * Applies all given transformations to the provided project.
     *
     * @param ProjectDescriptor $project
     * @param Transformation[]  $transformations
     *
     * @return void
     */
    private function transformProject(ProjectDescriptor $project, $transformations)
    {
        foreach ($transformations as $transformation) {
            $transformation->setTransformer($this);
            $this->applyTransformationToProject($transformation, $project);
        }
    }

    /**
     * Applies the given transformation to the provided project.
     *
     * This method will attempt to find an appropriate writer for the given transformation and invoke that with the
     * transformation and project so that an artifact can be generated that matches the intended transformation.
     *
     * In addition this method will emit the following events:
     *
     * - transformer.transformation.pre, before the project has been transformed with this transformation.
     * - transformer.transformation.post, after the project has been transformed with this transformation
     *
     * @param Transformation $transformation
     * @param ProjectDescriptor $project
     *
     * @uses Dispatcher to emit the events surrounding a transformation.
     *
     * @return void
     */
    private function applyTransformationToProject(Transformation $transformation, ProjectDescriptor $project)
    {
        $this->log(
            sprintf(
                '  Writer %s %s on %s',
                $transformation->getWriter(),
                ($transformation->getQuery() ? ' using query "' . $transformation->getQuery() . '"' : ''),
                $transformation->getArtifact()
            )
        );

        $preTransformationEvent = PreTransformationEvent::createInstance($this)->setTransformation($transformation);
        Dispatcher::getInstance()->dispatch(self::EVENT_PRE_TRANSFORMATION, $preTransformationEvent);

        $writer = $this->writers[$transformation->getWriter()];
        $writer->transform($project, $transformation);

        $postTransformationEvent = PostTransformationEvent::createInstance($this);
        Dispatcher::getInstance()->dispatch(self::EVENT_POST_TRANSFORMATION, $postTransformationEvent);
    }
}
