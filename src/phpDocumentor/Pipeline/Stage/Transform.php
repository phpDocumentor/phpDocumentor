<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline\Stage;

use Exception;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\FileSystem\FlySystemFactory;
use phpDocumentor\FlowService\ServiceProvider;
use phpDocumentor\Transformer\Event\PostTransformEvent;
use phpDocumentor\Transformer\Event\PreTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Event\WriterInitializationEvent;
use phpDocumentor\Transformer\Template\Factory;
use phpDocumentor\Transformer\Transformer;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use Psr\Log\LoggerInterface;

use function count;
use function current;
use function get_class;
use function sprintf;

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
    /** @var LoggerInterface */
    private $logger;

    /** @var Factory */
    private $templateFactory;

    /** @var FlySystemFactory */
    private $flySystemFactory;

    /** @var ServiceProvider */
    private $transformerProvider;

    /**
     * Initializes the command with all necessary dependencies to construct human-suitable output from the AST.
     */
    public function __construct(
        ServiceProvider $transformerProvider,
        FlySystemFactory $flySystemFactory,
        LoggerInterface $logger,
        Factory $templateFactory
    ) {
        $this->logger        = $logger;
        $this->templateFactory  = $templateFactory;
        $this->flySystemFactory = $flySystemFactory;
        $this->transformerProvider = $transformerProvider;

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
        $project = $payload->getBuilder()->getProjectDescriptor();

        foreach ($project->getVersions() as $version) {
            foreach ($version->getDocumentationSets() as $documentationSet) {
                $templates = $this->templateFactory->getTemplates(
                    $configuration['phpdocumentor']['templates'],
                    $this->flySystemFactory->createDestination($documentationSet)
                );
                $transformations = $templates->getTransformations();

                /** @var PreTransformEvent $preTransformEvent */
                $preTransformEvent = PreTransformEvent::createInstance($this);
                $preTransformEvent->setDocumentationSet($documentationSet);
                $preTransformEvent->setTransformations($transformations);
                Dispatcher::getInstance()->dispatch(
                    $preTransformEvent,
                    Transformer::EVENT_PRE_TRANSFORM
                );

                $this->transformerProvider->get($documentationSet)->execute(
                    $project,
                    $documentationSet,
                    current($templates)
                );

                /** @var PostTransformEvent $postTransformEvent */
                $postTransformEvent = PostTransformEvent::createInstance($this);
                $postTransformEvent->setDocumentationSet($documentationSet);
                $postTransformEvent->setTransformations($transformations);

                Dispatcher::getInstance()->dispatch($postTransformEvent, Transformer::EVENT_POST_TRANSFORM);
            }
        }

        return $payload;
    }

    /**
     * Connect a series of output messages to various events to display progress.
     */
    private function connectOutputToEvents(): void
    {
        $dispatcherInstance = Dispatcher::getInstance();
        $dispatcherInstance->addListener(
            Transformer::EVENT_PRE_TRANSFORM,
            function (PreTransformEvent $event): void {
                $transformations = $event->getTransformations();
                $this->logger->info(sprintf("\nApplying %d transformations", count($transformations)));
            }
        );
        $dispatcherInstance->addListener(
            Transformer::EVENT_PRE_INITIALIZATION,
            function (WriterInitializationEvent $event): void {
                if (!($event->getWriter() instanceof WriterAbstract)) {
                    return;
                }

                $this->logger->info('  Initialize writer "' . get_class($event->getWriter()) . '"');
            }
        );
        $dispatcherInstance->addListener(
            Transformer::EVENT_PRE_TRANSFORMATION,
            function (PreTransformationEvent $event): void {
                $this->logger->info(
                    '  Execute transformation using writer "' . $event->getTransformation()->getWriter() . '"'
                );
            }
        );
    }
}
