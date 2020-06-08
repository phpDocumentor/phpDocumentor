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
use phpDocumentor\Compiler\Compiler;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Dsn;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Reflection\DocBlock\ExampleFinder;
use phpDocumentor\Transformer\Event\PreTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Event\WriterInitializationEvent;
use phpDocumentor\Transformer\Transformer;
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
final class Transform
{
    /** @var Transformer $transformer Principal object for guiding the transformation process */
    private $transformer;

    /** @var Compiler $compiler Collection of pre-transformation actions (Compiler Passes) */
    private $compiler;

    /** @var LoggerInterface */
    private $logger;

    /** @var ExampleFinder */
    private $exampleFinder;

    /**
     * Initializes the command with all necessary dependencies to construct human-suitable output from the AST.
     */
    public function __construct(
        Transformer $transformer,
        Compiler $compiler,
        LoggerInterface $logger,
        ExampleFinder $exampleFinder
    ) {
        $this->transformer   = $transformer;
        $this->compiler      = $compiler;
        $this->exampleFinder = $exampleFinder;
        $this->logger        = $logger;

        $this->connectOutputToEvents();
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @throws Exception If the target location is not a folder.
     */
    public function __invoke(Payload $payload) : Payload
    {
        $configuration = $payload->getConfig();

        $this->setTargetLocationBasedOnDsn($configuration['phpdocumentor']['paths']['output']);
        $this->loadTemplatesBasedOnNames($configuration['phpdocumentor']['templates']);
        $this->provideLocationsOfExamples();

        $this->doTransform($payload->getBuilder());

        return $payload;
    }

    /**
     * Connect a series of output messages to various events to display progress.
     */
    private function connectOutputToEvents() : void
    {
        $dispatcherInstance = Dispatcher::getInstance();
        $dispatcherInstance->addListener(
            Transformer::EVENT_PRE_TRANSFORM,
            function (PreTransformEvent $event) : void {
                /** @var Transformer $transformer */
                $transformer     = $event->getSubject();
                $templates       = $transformer->getTemplates();
                $transformations = $templates->getTransformations();
                $this->logger->info(sprintf("\nApplying %d transformations", count($transformations)));
            }
        );
        $dispatcherInstance->addListener(
            Transformer::EVENT_PRE_INITIALIZATION,
            function (WriterInitializationEvent $event) : void {
                if (!($event->getWriter() instanceof WriterAbstract)) {
                    return;
                }

                $this->logger->info('  Initialize writer "' . get_class($event->getWriter()) . '"');
            }
        );
        $dispatcherInstance->addListener(
            Transformer::EVENT_PRE_TRANSFORMATION,
            function (PreTransformationEvent $event) : void {
                $this->logger->info(
                    '  Execute transformation using writer "' . $event->getTransformation()->getWriter() . '"'
                );
            }
        );
    }

    /**
     * @param array<string, string> $templateNames
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

    private function doTransform(ProjectDescriptorBuilder $builder) : void
    {
        /** @var CompilerPassInterface $pass */
        foreach ($this->compiler as $pass) {
            $pass->execute($builder->getProjectDescriptor());
        }
    }

    private function provideLocationsOfExamples() : void
    {
        //TODO: Should determine root based on filesystems. Could be an issue for multiple.
        // Need some config update here.
        $this->exampleFinder->setSourceDirectory(getcwd());
        $this->exampleFinder->setExampleDirectories(['.']);
    }
}
