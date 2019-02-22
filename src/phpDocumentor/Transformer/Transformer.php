<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer;

use InvalidArgumentException;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Transformer\Event\PostTransformationEvent;
use phpDocumentor\Transformer\Event\PostTransformEvent;
use phpDocumentor\Transformer\Event\PreTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Event\WriterInitializationEvent;
use phpDocumentor\Transformer\Writer\Initializable;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

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
    protected $transformations = [];

    private $logger;

    /**
     * Wires the template collection and writer collection to this transformer.
     */
    public function __construct(
        Template\Collection $templateCollection,
        Writer\Collection $writerCollection,
        LoggerInterface $logger
    ) {
        $this->templates = $templateCollection;
        $this->writers = $writerCollection;
        $this->logger = $logger;
    }

    public function getDescription(): string
    {
        return 'Transform analyzed project into artifacts';
    }

    /**
     * Sets the target location where to output the artifacts.
     *
     * @param string $target The target location where to output the artifacts.
     *
     * @throws InvalidArgumentException if the target is not a valid writable directory.
     */
    public function setTarget(string $target): void
    {
        $path = realpath($target);
        if (false === $path) {
            if (@mkdir($target, 0755, true)) {
                $path = realpath($target);
            } else {
                throw new InvalidArgumentException(
                    'Target directory (' . $target . ') does not exist and could not be created'
                );
            }
        }

        if (!is_dir($path) || !is_writable($path)) {
            throw new InvalidArgumentException('Given target (' . $target . ') is not a writable directory');
        }

        $this->target = $path;
    }

    /**
     * Returns the location where to store the artifacts.
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * Returns the list of templates which are going to be adopted.
     */
    public function getTemplates(): Template\Collection
    {
        return $this->templates;
    }

    /**
     * Transforms the given project into a series of artifacts as provided by the templates.
     */
    public function execute(ProjectDescriptor $project): void
    {
        /** @var PreTransformEvent $preTransformEvent */
        $preTransformEvent = PreTransformEvent::createInstance($this);
        $preTransformEvent->setProject($project);
        Dispatcher::getInstance()->dispatch(
            self::EVENT_PRE_TRANSFORM,
            $preTransformEvent
        );

        $transformations = $this->getTemplates()->getTransformations();
        $this->initializeWriters($project, $transformations);
        $this->transformProject($project, $transformations);

        Dispatcher::getInstance()->dispatch(self::EVENT_POST_TRANSFORM, PostTransformEvent::createInstance($this));

        $this->logger->log(LogLevel::NOTICE, 'Finished transformation process');
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
     */
    public function generateFilename(string $name): string
    {
        if (substr($name, -4) === '.php') {
            $name = substr($name, 0, -4);
        }

        return trim(str_replace([DIRECTORY_SEPARATOR, '\\'], '.', trim($name, DIRECTORY_SEPARATOR . '.')), '.')
            . '.html';
    }

    /**
     * Dispatches a logging request.
     *
     * This method can be used by writers to output logs without having to know anything about
     * the logging mechanism of phpDocumentor.
     */
    public function log(string $message, string $priority = LogLevel::INFO): void
    {
        $this->logger->log($priority, $message);
    }

    /**
     * Dispatches a logging request to log a debug message.
     *
     * This method can be used by writers to output logs without having to know anything about
     * the logging mechanism of phpDocumentor.
     */
    public function debug(string $message): void
    {
        $this->log($message, LogLevel::DEBUG);
    }

    /**
     * Initializes all writers that are used during this transformation.
     *
     * @param Transformation[] $transformations
     */
    private function initializeWriters(ProjectDescriptor $project, array $transformations): void
    {
        $isInitialized = [];
        foreach ($transformations as $transformation) {
            $writerName = $transformation->getWriter();

            if (in_array($writerName, $isInitialized, true)) {
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
     * @uses Dispatcher to emit the events surrounding an initialization.
     */
    private function initializeWriter(WriterAbstract $writer, ProjectDescriptor $project): void
    {
        /** @var WriterInitializationEvent $instance */
        $instance = WriterInitializationEvent::createInstance($this);
        $event = $instance->setWriter($writer);
        Dispatcher::getInstance()->dispatch(self::EVENT_PRE_INITIALIZATION, $event);

        if ($writer instanceof Initializable) {
            $writer->initialize($project);
        }

        Dispatcher::getInstance()->dispatch(self::EVENT_POST_INITIALIZATION, $event);
    }

    /**
     * Applies all given transformations to the provided project.
     *
     * @param Transformation[] $transformations
     */
    private function transformProject(ProjectDescriptor $project, array $transformations): void
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
     * @uses Dispatcher to emit the events surrounding a transformation.
     */
    private function applyTransformationToProject(Transformation $transformation, ProjectDescriptor $project): void
    {
        $this->logger->log(
            LogLevel::NOTICE,
            sprintf(
                '  Writer %s %s on %s',
                $transformation->getWriter(),
                ($transformation->getQuery() ? ' using query "' . $transformation->getQuery() . '"' : ''),
                $transformation->getArtifact()
            )
        );

        /** @var PreTransformationEvent $preTransformationEvent */
        $preTransformationEvent = PreTransformationEvent::create($this, $transformation);
        Dispatcher::getInstance()->dispatch(self::EVENT_PRE_TRANSFORMATION, $preTransformationEvent);

        $writer = $this->writers[$transformation->getWriter()];
        $writer->transform($project, $transformation);

        $postTransformationEvent = PostTransformationEvent::createInstance($this);
        Dispatcher::getInstance()->dispatch(self::EVENT_POST_TRANSFORMATION, $postTransformationEvent);
    }
}
