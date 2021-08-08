<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline\Stage;

use Exception;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Dsn;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Transformer\Event\PostTransformEvent;
use phpDocumentor\Transformer\Event\PreTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Event\WriterInitializationEvent;
use phpDocumentor\Transformer\Template\Factory;
use phpDocumentor\Transformer\Transformer;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

use function count;
use function get_class;
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
    /** @var Transformer $transformer Principal object for guiding the transformation process */
    private $transformer;

    /** @var LoggerInterface */
    private $logger;

    /** @var Factory */
    private $templateFactory;

    /** @var FlySystemFactory */
    private $flySystemFactory;

    /**
     * Initializes the command with all necessary dependencies to construct human-suitable output from the AST.
     */
    public function __construct(
        Transformer $transformer,
        FlySystemFactory $flySystemFactory,
        LoggerInterface $logger,
        Factory $templateFactory
    ) {
        $this->transformer   = $transformer;
        $this->logger        = $logger;
        $this->templateFactory  = $templateFactory;
        $this->flySystemFactory = $flySystemFactory;

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

        $templates = $this->templateFactory->getTemplates(
            $configuration['phpdocumentor']['templates'],
            $this->createFileSystem($configuration['phpdocumentor']['paths']['output'])
        );
        $project = $payload->getBuilder()->getProjectDescriptor();
        $transformations = $templates->getTransformations();

        /** @var PreTransformEvent $preTransformEvent */
        $preTransformEvent = PreTransformEvent::createInstance($this);
        $preTransformEvent->setProject($project);
        $preTransformEvent->setTransformations($transformations);
        Dispatcher::getInstance()->dispatch(
            $preTransformEvent,
            Transformer::EVENT_PRE_TRANSFORM
        );

        $this->transformer->execute(
            $project,
            $transformations
        );

        /** @var PostTransformEvent $postTransformEvent */
        $postTransformEvent = PostTransformEvent::createInstance($this);
        $postTransformEvent->setProject($project);
        $postTransformEvent->setTransformations($transformations);

        Dispatcher::getInstance()->dispatch($postTransformEvent, Transformer::EVENT_POST_TRANSFORM);

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

    private function createFileSystem(Dsn $dsn): FilesystemInterface
    {
        $target     = $dsn->getPath();
        $fileSystem = new Filesystem();
        if (!$fileSystem->isAbsolutePath((string) $target)) {
            $target = getcwd() . DIRECTORY_SEPARATOR . $target;
        }

        $destination = $this->flySystemFactory->create(Dsn::createFromString((string) $target));

        //TODO: the guides to need this, can we get rid of these lines?
        $this->transformer->setTarget((string) $target);
        $this->transformer->setDestination($destination);

        return $destination;
    }
}
