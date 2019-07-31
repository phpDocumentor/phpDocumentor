<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Stage;

use Exception;
use phpDocumentor\Compiler\Compiler;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
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

/**
 * Transforms the structure file into the specified output format
 *
 * This task will execute the transformation rules described in the given
 * template (defaults to 'responsive') with the given source (defaults to
 * output/structure.xml) and writes these to the target location (defaults to
 * 'output').
 *
 * It is possible for the user to receive additional information using the
 * verbose option or stop additional information using the quiet option. Please
 * take note that the quiet option also disables logging to file.
 */
final class Transform
{
    /** @var ProjectDescriptorBuilder $builder Object containing the project meta-data and AST */
    private $builder;

    /** @var Transformer $transformer Principal object for guiding the transformation process */
    private $transformer;

    /** @var Compiler $compiler Collection of pre-transformation actions (Compiler Passes) */
    private $compiler;

    /** @var LoggerInterface */
    private $logger;
    /**
     * @var ExampleFinder
     */
    private $exampleFinder;

    /**
     * Initializes the command with all necessary dependencies to construct human-suitable output from the AST.
     */
    public function __construct(
        ProjectDescriptorBuilder $builder,
        Transformer $transformer,
        Compiler $compiler,
        LoggerInterface $logger,
        ExampleFinder $exampleFinder
    ) {
        $this->builder = $builder;
        $this->transformer = $transformer;
        $this->compiler = $compiler;
        $this->logger = $logger;

        $this->connectOutputToEvents();
        $this->exampleFinder = $exampleFinder;
    }

    /**
     * Returns the builder object containing the AST and other meta-data.
     */
    private function getBuilder(): ProjectDescriptorBuilder
    {
        return $this->builder;
    }

    /**
     * Returns the transformer used to guide the transformation process from AST to output.
     */
    private function getTransformer(): Transformer
    {
        return $this->transformer;
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @throws Exception if the target location is not a folder.
     */
    public function __invoke(Payload $payload): Payload
    {
        $transformer = $this->getTransformer();
        $configuration = $payload->getConfig();

        $target = $configuration['phpdocumentor']['paths']['output']->getPath();
        $fileSystem = new Filesystem();
        if (! $fileSystem->isAbsolutePath((string) $target)) {
            $target = getcwd() . DIRECTORY_SEPARATOR . $target;
        }

        $transformer->setTarget((string) $target);

        $projectDescriptor = $this->getBuilder()->getProjectDescriptor();

        $stopWatch = new Stopwatch();

        foreach (array_column($configuration['phpdocumentor']['templates'], 'name') as $template) {
            $stopWatch->start('load template');
            $this->transformer->getTemplates()->load($template);
            $stopWatch->stop('load template');
        }

        //TODO: Should determine root based on filesystems. Could be an issue for multiple.
        // Need some config update here.
        $this->exampleFinder->setSourceDirectory(getcwd());
        $this->exampleFinder->setExampleDirectories(['.']);

        /** @var \phpDocumentor\Compiler\CompilerPassInterface $pass */
        foreach ($this->compiler as $pass) {
            $pass->execute($projectDescriptor);
        }

        return $payload;
    }

    /**
     * Connect a series of output messages to various events to display progress.
     */
    private function connectOutputToEvents(): void
    {
        Dispatcher::getInstance()->addListener(
            Transformer::EVENT_PRE_TRANSFORM,
            function (PreTransformEvent $event) {
                /** @var Transformer $transformer */
                $transformer = $event->getSubject();
                $templates = $transformer->getTemplates();
                $transformations = $templates->getTransformations();
                $this->logger->info(sprintf("\nApplying %d transformations", count($transformations)));
            }
        );
        Dispatcher::getInstance()->addListener(
            Transformer::EVENT_PRE_INITIALIZATION,
            function (WriterInitializationEvent $event) {
                if ($event->getWriter() instanceof WriterAbstract) {
                    $this->logger->info('  Initialize writer "' . get_class($event->getWriter()) . '"');
                }
            }
        );
        Dispatcher::getInstance()->addListener(
            Transformer::EVENT_PRE_TRANSFORMATION,
            function (PreTransformationEvent $event) {
                if ($event->getTransformation()->getWriter() instanceof WriterAbstract) {
                    $this->logger->info(
                        '  Execute transformation using writer "' . $event->getTransformation()->getWriter() . '"'
                    );
                }
            }
        );
    }
}
