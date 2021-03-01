<?php

declare(strict_types=1);

namespace phpDocumentor\FlowService\Api;

use Exception;
use phpDocumentor\Configuration\Configuration;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Dsn;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\FlowService\FlowService;
use phpDocumentor\Reflection\DocBlock\ExampleFinder;
use phpDocumentor\Transformer\Event\PreTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Event\WriterInitializationEvent;
use phpDocumentor\Transformer\Transformer as RealTransformer;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Stopwatch\Stopwatch;
use function array_column;
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
class Transformer implements FlowService
{
    /** @var RealTransformer $transformer Principal object for guiding the transformation process */
    private $transformer;

    /** @var LoggerInterface */
    private $logger;

    /** @var ExampleFinder */
    private $exampleFinder;

    /** @var Configuration */
    private $configuration;

    /**
     * Initializes the command with all necessary dependencies to construct human-suitable output from the AST.
     */
    public function __construct(
        RealTransformer $transformer,
        LoggerInterface $logger,
        ExampleFinder $exampleFinder,
        Configuration $configuration
    ) {
        $this->transformer   = $transformer;
        $this->exampleFinder = $exampleFinder;
        $this->logger        = $logger;
        $this->configuration = $configuration;

        $this->connectOutputToEvents();
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @throws Exception If the target location is not a folder.
     */
    public function operate(DocumentationSetDescriptor $documentationSet): void
    {
        $configuration = $this->configuration;

        $this->setTargetLocationBasedOnDsn($configuration['phpdocumentor']['paths']['output']);
        $this->loadTemplatesBasedOnNames($configuration['phpdocumentor']['templates']);
        $this->provideLocationsOfExamples();

        $this->transformer->execute($documentationSet);
    }

    /**
     * Connect a series of output messages to various events to display progress.
     */
    private function connectOutputToEvents() : void
    {
        $dispatcherInstance = Dispatcher::getInstance();
        $dispatcherInstance->addListener(
            RealTransformer::EVENT_PRE_TRANSFORM,
            function (PreTransformEvent $event) : void {
                /** @var RealTransformer $transformer */
                $transformer     = $event->getSubject();
                $templates       = $transformer->getTemplates();
                $transformations = $templates->getTransformations();
                $this->logger->info(sprintf("\nApplying %d transformations", count($transformations)));
            }
        );
        $dispatcherInstance->addListener(
            RealTransformer::EVENT_PRE_INITIALIZATION,
            function (WriterInitializationEvent $event) : void {
                if (!($event->getWriter() instanceof WriterAbstract)) {
                    return;
                }

                $this->logger->info('  Initialize writer "' . get_class($event->getWriter()) . '"');
            }
        );
        $dispatcherInstance->addListener(
            RealTransformer::EVENT_PRE_TRANSFORMATION,
            function (PreTransformationEvent $event) : void {
                $this->logger->info(
                    '  Execute transformation using writer "' . $event->getTransformation()->getWriter() . '"'
                );
            }
        );
    }

    /**
     * @param array<int, string> $templateNames
     */
    private function loadTemplatesBasedOnNames(array $templateNames) : void
    {
        $stopWatch = new Stopwatch();
        foreach (array_column($templateNames, 'name') as $template) {
            $stopWatch->start('load template');
            $this->transformer->getTemplates()->load($this->transformer, $template);
            $stopWatch->stop('load template');
        }
    }

    private function setTargetLocationBasedOnDsn(Dsn $dsn) : void
    {
        $target     = $dsn->getPath();
        $fileSystem = new Filesystem();
        if (!$fileSystem->isAbsolutePath((string) $target)) {
            $target = getcwd() . DIRECTORY_SEPARATOR . $target;
        }

        $this->transformer->setTarget((string) $target);
    }

    private function provideLocationsOfExamples() : void
    {
        //TODO: Should determine root based on filesystems. Could be an issue for multiple.
        // Need some config update here.
        $this->exampleFinder->setSourceDirectory(getcwd());
        $this->exampleFinder->setExampleDirectories(['.']);
    }
}
