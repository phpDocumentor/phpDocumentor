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
                'Given target (' . $target . ') is not a writable directory'
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
}
