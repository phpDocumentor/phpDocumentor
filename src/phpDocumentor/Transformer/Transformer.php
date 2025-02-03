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
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\FileSystem\FileSystem;
use phpDocumentor\Transformer\Event\PostTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformationEvent;
use phpDocumentor\Transformer\Event\WriterInitializationEvent;
use phpDocumentor\Transformer\Writer\Initializable;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

use function in_array;
use function sprintf;

/**
 * Core class responsible for transforming the cache file to a set of artifacts.
 */
class Transformer
{
    final public const EVENT_PRE_TRANSFORMATION = 'transformer.transformation.pre';

    final public const EVENT_POST_TRANSFORMATION = 'transformer.transformation.post';

    final public const EVENT_PRE_INITIALIZATION = 'transformer.writer.initialization.pre';

    final public const EVENT_POST_INITIALIZATION = 'transformer.writer.initialization.post';

    final public const EVENT_PRE_TRANSFORM = 'transformer.transform.pre';

    final public const EVENT_POST_TRANSFORM = 'transformer.transform.post';

    /** @var FilesystemInterface|null $destination The destination filesystem to write to */
    private FileSystem $destination;

    /** @var Writer\Collection $writers */
    protected $writers;

    /**
     * Wires the template collection and writer collection to this transformer.
     */
    public function __construct(
        Writer\Collection $writerCollection,
        private readonly LoggerInterface $logger,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        $this->writers = $writerCollection;
    }

    public function getDescription(): string
    {
        return 'Transform analyzed project into artifacts';
    }

    public function destination(): FileSystem
    {
        return $this->destination;
    }

    /**
     * Transforms the given project into a series of artifacts as provided by the templates.
     *
     * @param Transformation[] $transformations
     */
    public function execute(
        FileSystem $destination,
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        array $transformations,
    ): void {
        $this->destination = $destination;
        $this->initializeWriters($project, $documentationSet, $transformations);
        $this->transform($project, $documentationSet, $transformations);

        $this->logger->log(LogLevel::NOTICE, 'Finished transformation process');
    }

    /**
     * Initializes all writers that are used during this transformation.
     *
     * @param Transformation[] $transformations
     */
    private function initializeWriters(
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        array $transformations,
    ): void {
        $isInitialized = [];
        foreach ($transformations as $transformation) {
            $writerName = $transformation->getWriter();

            if (in_array($writerName, $isInitialized, true)) {
                continue;
            }

            $isInitialized[] = $writerName;
            $writer = $this->writers->get($writerName);
            $this->initializeWriter($writer, $project, $documentationSet, $transformation->template());
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
    private function initializeWriter(
        WriterAbstract $writer,
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        Template $template,
    ): void {
        /** @var WriterInitializationEvent $instance */
        $instance = WriterInitializationEvent::createInstance($this);
        $event = $instance->setWriter($writer);
        $this->eventDispatcher->dispatch($event, self::EVENT_PRE_INITIALIZATION);

        if ($writer instanceof Initializable) {
            $writer->initialize($project, $documentationSet, $template);
        }

        $this->eventDispatcher->dispatch($event, self::EVENT_POST_INITIALIZATION);
    }

    /**
     * Applies all given transformations to the provided project.
     *
     * @param Transformation[] $transformations
     */
    private function transform(
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        array $transformations,
    ): void {
        foreach ($transformations as $transformation) {
            $transformation->setTransformer($this);
            $this->applyTransformation($transformation, $project, $documentationSet);
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
    private function applyTransformation(
        Transformation $transformation,
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
    ): void {
        $this->logger->log(
            LogLevel::NOTICE,
            sprintf(
                '  Writer %s %s on %s',
                $transformation->getWriter(),
                ($transformation->getQuery() ? ' using query "' . $transformation->getQuery() . '"' : ''),
                $transformation->getArtifact(),
            ),
        );

        $preTransformationEvent = PreTransformationEvent::create($this, $transformation);
        $this->eventDispatcher->dispatch($preTransformationEvent, self::EVENT_PRE_TRANSFORMATION);

        $writer = $this->writers->get($transformation->getWriter());
        $writer->transform($transformation, $project, $documentationSet);

        $postTransformationEvent = PostTransformationEvent::createInstance($this);
        $this->eventDispatcher->dispatch($postTransformationEvent, self::EVENT_POST_TRANSFORMATION);
    }
}
