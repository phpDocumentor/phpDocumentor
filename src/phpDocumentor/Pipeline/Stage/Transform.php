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

namespace phpDocumentor\Pipeline\Stage;

use Exception;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Dsn;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Transformer\Event\PostTransformEvent;
use phpDocumentor\Transformer\Event\PreTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Event\WriterInitializationEvent;
use phpDocumentor\Transformer\Template\Factory;
use phpDocumentor\Transformer\Transformer;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\Assert\Assert;

use function count;
use function getcwd;
use function sprintf;

use const DIRECTORY_SEPARATOR;

/**
 * Transforms the structure file into the specified output format
 *
 * This task will execute the transformation rules described in the given
 * template with the given source and writes these to the target location
 * (defaults to 'output').
 *
 * It is possible for the user to receive additional information using the
 * verbose option or stop additional information using the quiet option. Please
 * take note that the quiet option also disables logging to file.
 */
class Transform
{
    /**
     * Initializes the command with all necessary dependencies to construct human-suitable output from the AST.
     */
    public function __construct(
        /** @var Transformer $transformer Principal object for guiding the transformation process */
        private readonly Transformer $transformer,
        private readonly FlySystemFactory $flySystemFactory,
        private readonly LoggerInterface $logger,
        private readonly Factory $templateFactory,
        private readonly EventDispatcher $eventDispatcher,
    ) {
        $this->connectOutputToEvents();
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @throws Exception If the target location is not a folder.
     */
    public function __invoke(Payload $payload): Payload
    {
        $configuration = $payload->getConfig();

        Assert::keyExists($configuration['phpdocumentor'], 'paths');

        $templates = $this->templateFactory->getTemplates(
            $configuration['phpdocumentor']['templates'],
            $this->createFileSystem($configuration['phpdocumentor']['paths']['output']),
        );
        $project = $payload->getBuilder()->getProjectDescriptor();
        $transformations = $templates->getTransformations();

        /** @var PreTransformEvent $preTransformEvent */
        $preTransformEvent = PreTransformEvent::createInstance($this);
        $preTransformEvent->setProject($project);
        $preTransformEvent->setTransformations($transformations);
        $this->eventDispatcher->dispatch(
            $preTransformEvent,
            Transformer::EVENT_PRE_TRANSFORM,
        );

        foreach ($project->getVersions() as $version) {
            $documentationSets = $version->getDocumentationSets();
            foreach ($documentationSets as $documentationSet) {
                $this->transformer->execute($project, $documentationSet, $transformations);
            }
        }

        /** @var PostTransformEvent $postTransformEvent */
        $postTransformEvent = PostTransformEvent::createInstance($this);
        $postTransformEvent->setProject($project);
        $postTransformEvent->setTransformations($transformations);

        $this->eventDispatcher->dispatch($postTransformEvent, Transformer::EVENT_POST_TRANSFORM);

        return $payload;
    }

    /**
     * Connect a series of output messages to various events to display progress.
     */
    private function connectOutputToEvents(): void
    {
        $this->eventDispatcher->addListener(
            Transformer::EVENT_PRE_TRANSFORM,
            function (PreTransformEvent $event): void {
                $transformations = $event->getTransformations();
                $this->logger->info(sprintf("\nApplying %d transformations", count($transformations)));
            },
        );
        $this->eventDispatcher->addListener(
            Transformer::EVENT_PRE_INITIALIZATION,
            function (WriterInitializationEvent $event): void {
                if (! ($event->getWriter() instanceof WriterAbstract)) {
                    return;
                }

                $this->logger->info('  Initialize writer "' . $event->getWriter()::class . '"');
            },
        );
        $this->eventDispatcher->addListener(
            Transformer::EVENT_PRE_TRANSFORMATION,
            function (PreTransformationEvent $event): void {
                $this->logger->info(
                    '  Execute transformation using writer "' . $event->getTransformation()->getWriter() . '"',
                );
            },
        );
    }

    private function createFileSystem(Dsn $dsn): FilesystemInterface
    {
        $target     = $dsn->getPath();
        $fileSystem = new Filesystem();
        if (! $fileSystem->isAbsolutePath((string) $target)) {
            $target = getcwd() . DIRECTORY_SEPARATOR . $target;
        }

        return $this->flySystemFactory->create(Dsn::createFromString((string) $target));
    }
}
