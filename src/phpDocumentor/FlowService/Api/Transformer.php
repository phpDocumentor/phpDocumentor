<?php

declare(strict_types=1);

namespace phpDocumentor\FlowService\Api;

use Exception;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\FlowService\Transformer as TransformerInterface;
use phpDocumentor\Transformer\Event\PreTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Event\WriterInitializationEvent;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformer as RealTransformer;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use Psr\Log\LoggerInterface;
use function count;
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
class Transformer implements TransformerInterface
{
    /** @var RealTransformer $transformer Principal object for guiding the transformation process */
    private $transformer;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Initializes the command with all necessary dependencies to construct human-suitable output from the AST.
     */
    public function __construct(
        RealTransformer $transformer,
        LoggerInterface $logger
    ) {
        $this->transformer   = $transformer;
        $this->logger        = $logger;

        $this->connectOutputToEvents();
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @throws Exception If the target location is not a folder.
     */
    public function execute(ProjectDescriptor $project, DocumentationSetDescriptor $documentationSet, Template $template) : void
    {
        $this->transformer->execute($project, $documentationSet, $template);
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
                $transformations = $event->getTransformations();
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
}
