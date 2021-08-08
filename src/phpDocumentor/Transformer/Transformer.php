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

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Dsn;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Transformer\Event\PostTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformationEvent;
use phpDocumentor\Transformer\Event\WriterInitializationEvent;
use phpDocumentor\Transformer\Writer\Initializable;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

use function in_array;
use function sprintf;

/**
 * Core class responsible for transforming the cache file to a set of artifacts.
 */
class Transformer
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

    /** @var Writer\Collection $writers */
    protected $writers;

    /** @var LoggerInterface */
    private $logger;

    /** @var FlySystemFactory */
    private $flySystemFactory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * Wires the template collection and writer collection to this transformer.
     */
    public function __construct(
        Writer\Collection $writerCollection,
        LoggerInterface $logger,
        FlySystemFactory $flySystemFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->writers = $writerCollection;
        $this->logger = $logger;
        $this->flySystemFactory = $flySystemFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getDescription(): string
    {
        return 'Transform analyzed project into artifacts';
    }

    /**
     * Sets the target location where to output the artifacts.
     *
     * @param string $target The target location where to output the artifacts.
     */
    public function setTarget(string $target): void
    {
        $this->target = $target;
        $this->destination = $this->flySystemFactory->create(Dsn::createFromString($target));
    }

    /**
     * Returns the location where to store the artifacts.
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setDestination(FilesystemInterface $filesystem): void
    {
        $this->destination = $filesystem;
    }

    public function destination(): FilesystemInterface
    {
        $destination = $this->destination;

        Assert::notNull($destination);

        return $destination;
    }

    /**
     * Transforms the given project into a series of artifacts as provided by the templates.
     *
     * @param Transformation[] $transformations
     */
    public function execute(ProjectDescriptor $project, array $transformations): void
    {
        $this->initializeWriters($project, $transformations);
        $this->transformProject($project, $transformations);

        $this->logger->log(LogLevel::NOTICE, 'Finished transformation process');
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
            $this->initializeWriter($writer, $project, $transformation->template());
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
    private function initializeWriter(WriterAbstract $writer, ProjectDescriptor $project, Template $template): void
    {
        /** @var WriterInitializationEvent $instance */
        $instance = WriterInitializationEvent::createInstance($this);
        $event = $instance->setWriter($writer);
        $this->eventDispatcher->dispatch($event, self::EVENT_PRE_INITIALIZATION);

        if ($writer instanceof Initializable) {
            $writer->initialize($project, $template);
        }

        $this->eventDispatcher->dispatch($event, self::EVENT_POST_INITIALIZATION);
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

        $preTransformationEvent = PreTransformationEvent::create($this, $transformation);
        $this->eventDispatcher->dispatch($preTransformationEvent, self::EVENT_PRE_TRANSFORMATION);

        $writer = $this->writers[$transformation->getWriter()];
        $writer->transform($project, $transformation);

        $postTransformationEvent = PostTransformationEvent::createInstance($this);
        $this->eventDispatcher->dispatch($postTransformationEvent, self::EVENT_POST_TRANSFORMATION);
    }
}
