<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Transformer;

use InvalidArgumentException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Dsn;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Transformer\Event\PostTransformationEvent;
use phpDocumentor\Transformer\Event\PostTransformEvent;
use phpDocumentor\Transformer\Event\PreTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Event\WriterInitializationEvent;
use phpDocumentor\Transformer\Writer\Initializable;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;
use function in_array;
use function sprintf;

/**
 * Core class responsible for transforming the cache file to a set of artifacts.
 */
class Transformer implements CompilerPassInterface
{
    public const EVENT_PRE_TRANSFORMATION = 'transformer.transformation.pre';

    public const EVENT_POST_TRANSFORMATION = 'transformer.transformation.post';

    public const EVENT_PRE_INITIALIZATION = 'transformer.writer.initialization.pre';

    public const EVENT_POST_INITIALIZATION = 'transformer.writer.initialization.post';

    public const EVENT_PRE_TRANSFORM = 'transformer.transform.pre';

    public const EVENT_POST_TRANSFORM = 'transformer.transform.post';

    /** @var int represents the priority in the Compiler queue. */
    public const COMPILER_PRIORITY = 5000;

    /** @var string|null $target Target location where to output the artifacts */
    protected $target = null;

    /** @var FilesystemInterface|null $destination The destination filesystem to write to */
    private $destination = null;

    /** @var Template\Collection $templates */
    protected $templates;

    /** @var Writer\Collection $writers */
    protected $writers;

    /** @var Transformation[] $transformations */
    protected $transformations = [];

    /** @var LoggerInterface */
    private $logger;

    /** @var FlySystemFactory */
    private $flySystemFactory;

    /**
     * Wires the template collection and writer collection to this transformer.
     */
    public function __construct(
        Template\Collection $templateCollection,
        Writer\Collection $writerCollection,
        LoggerInterface $logger,
        FlySystemFactory $flySystemFactory
    ) {
        $this->templates = $templateCollection;
        $this->writers = $writerCollection;
        $this->logger = $logger;
        $this->flySystemFactory = $flySystemFactory;
    }

    public function getDescription() : string
    {
        return 'Transform analyzed project into artifacts';
    }

    /**
     * Sets the target location where to output the artifacts.
     *
     * @param string $target The target location where to output the artifacts.
     */
    public function setTarget(string $target) : void
    {
        $this->target = $target;
        $this->destination = $this->flySystemFactory->create(Dsn::createFromString($target));
    }

    /**
     * Returns the location where to store the artifacts.
     */
    public function getTarget() : ?string
    {
        return $this->target;
    }

    public function destination() : FilesystemInterface
    {
        return $this->destination;
    }

    public function getTemplatesDirectory() : Filesystem
    {
        $dsnString = $this->getTemplates()->getTemplatesPath();

        try {
            $filesystem = $this->flySystemFactory->create(Dsn::createFromString($dsnString));
        } catch (InvalidArgumentException $e) {
            throw new RuntimeException(
                'Unable to access the folder with the global templates, received DSN is: ' . $dsnString
            );
        }

        return $filesystem;
    }

    /**
     * Returns the list of templates which are going to be adopted.
     */
    public function getTemplates() : Template\Collection
    {
        return $this->templates;
    }

    /**
     * Transforms the given project into a series of artifacts as provided by the templates.
     */
    public function execute(ProjectDescriptor $project) : void
    {
        /** @var PreTransformEvent $preTransformEvent */
        $preTransformEvent = PreTransformEvent::createInstance($this);
        $preTransformEvent->setProject($project);
        Dispatcher::getInstance()->dispatch(
            $preTransformEvent,
            self::EVENT_PRE_TRANSFORM
        );

        $transformations = $this->getTemplates()->getTransformations();
        $this->initializeWriters($project, $transformations);
        $this->transformProject($project, $transformations);

        /** @var PostTransformEvent $postTransformEvent */
        $postTransformEvent = PostTransformEvent::createInstance($this);
        $postTransformEvent->setProject($project);

        Dispatcher::getInstance()->dispatch($postTransformEvent, self::EVENT_POST_TRANSFORM);

        $this->logger->log(LogLevel::NOTICE, 'Finished transformation process');
    }

    /**
     * Initializes all writers that are used during this transformation.
     *
     * @param Transformation[] $transformations
     */
    private function initializeWriters(ProjectDescriptor $project, array $transformations) : void
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
    private function initializeWriter(WriterAbstract $writer, ProjectDescriptor $project) : void
    {
        /** @var WriterInitializationEvent $instance */
        $instance = WriterInitializationEvent::createInstance($this);
        $event = $instance->setWriter($writer);
        Dispatcher::getInstance()->dispatch($event, self::EVENT_PRE_INITIALIZATION);

        if ($writer instanceof Initializable) {
            $writer->initialize($project);
        }

        Dispatcher::getInstance()->dispatch($event, self::EVENT_POST_INITIALIZATION);
    }

    /**
     * Applies all given transformations to the provided project.
     *
     * @param Transformation[] $transformations
     */
    private function transformProject(ProjectDescriptor $project, array $transformations) : void
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
    private function applyTransformationToProject(Transformation $transformation, ProjectDescriptor $project) : void
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

        $preTransformationEvent = PreTransformationEvent::create($this, $transformation);
        Dispatcher::getInstance()->dispatch($preTransformationEvent, self::EVENT_PRE_TRANSFORMATION);

        $writer = $this->writers[$transformation->getWriter()];
        $writer->transform($project, $transformation);

        $postTransformationEvent = PostTransformationEvent::createInstance($this);
        Dispatcher::getInstance()->dispatch($postTransformationEvent, self::EVENT_POST_TRANSFORMATION);
    }
}
