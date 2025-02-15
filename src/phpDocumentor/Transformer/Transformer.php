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

    final public const COMPILER_PRIORITY = 5000;

    /** @var string|null $target Target location where to output the artifacts */
    protected $target = null;

    /** @var FilesystemInterface|null $destination The destination filesystem to write to */
    private FilesystemInterface|null $destination = null;

    /** @var Writer\Collection $writers */
    protected $writers;

    /**
     * Wires the template collection and writer collection to this transformer.
     */
    public function __construct(
        Writer\Collection $writerCollection,
        private readonly LoggerInterface $logger,
        private readonly FlySystemFactory $flySystemFactory,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        $this->writers = $writerCollection;
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
    public function getTarget(): string|null
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
    public function execute(
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
        array $transformations,
    ): void {
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
     * @uses EventDispatcherInterface to emit the events surrounding an initialization.
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
            '  Writer {writer} {querty} on {artifact}',
            [
                'writer' => $transformation->getWriter(),
                'query' => ($transformation->getQuery() ? ' using query "' . $transformation->getQuery() . '"' : ''),
                'artifact' => $transformation->getArtifact(),
            ],
        );

        $preTransformationEvent = PreTransformationEvent::create($this, $transformation);
        $this->eventDispatcher->dispatch($preTransformationEvent, self::EVENT_PRE_TRANSFORMATION);

        $writer = $this->writers->get($transformation->getWriter());
        $writer->transform($transformation, $project, $documentationSet);

        $postTransformationEvent = PostTransformationEvent::createInstance($this);
        $this->eventDispatcher->dispatch($postTransformationEvent, self::EVENT_POST_TRANSFORMATION);
    }
}
