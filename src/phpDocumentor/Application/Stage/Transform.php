<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Stage;

use phpDocumentor\Compiler\Compiler;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Transformer\Event\PreTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Event\WriterInitializationEvent;
use phpDocumentor\Transformer\Transformer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Zend\Cache\Storage\StorageInterface;

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

    /**
     * @var StorageInterface
     */
    private $cache;

    private $logger;

    /**
     * Initializes the command with all necessary dependencies to construct human-suitable output from the AST.
     */
    public function __construct(
        ProjectDescriptorBuilder $builder,
        Transformer $transformer,
        Compiler $compiler,
        StorageInterface $cache,
        LoggerInterface $logger
    ) {
        $this->builder = $builder;
        $this->transformer = $transformer;
        $this->compiler = $compiler;
        $this->cache = $cache;
        $this->logger = $logger;

        $this->connectOutputToEvents();
    }

    /**
     * Returns the builder object containing the AST and other meta-data.
     *
     * @return ProjectDescriptorBuilder
     */
    private function getBuilder()
    {
        return $this->builder;
    }

    /**
     * Returns the transformer used to guide the transformation process from AST to output.
     *
     * @return Transformer
     */
    private function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @return integer
     * @throws \Exception if the target location is not a folder.
     */
    public function __invoke(array $configuration)
    {
        // initialize transformer
        $transformer = $this->getTransformer();

        $projectDescriptor = $this->getBuilder()->getProjectDescriptor();
        $mapper = new ProjectDescriptorMapper($this->getCache());

        $stopWatch = new Stopwatch();
        $stopWatch->start('cache');
        $mapper->populate($projectDescriptor);
        $stopWatch->stop('cache');

        foreach (array_column($configuration['phpdocumentor']['templates'], 'name') as $template) {
            $stopWatch->start('load template');
            $this->transformer->getTemplates()->load($template);
//            $output->writeTimedLog(
//                'Preparing template "'. $template .'"',
//                array($transformer->getTemplates(), 'load'),
//                array($template, $transformer)
//            );
            $stopWatch->stop('load template');
        }

//        $output->writeTimedLog(
//            'Preparing ' . count($transformer->getTemplates()->getTransformations()) . ' transformations',
//            array($this, 'loadTransformations'),
//            array($transformer)
//        );

        //$this->loadTransformations($transformer);

//        if ($progress) {
//            $progress->start($output, count($transformer->getTemplates()->getTransformations()));
//        }

        /** @var CompilerPassInterface $pass */
        foreach ($this->compiler as $pass) {
            $pass->execute($projectDescriptor);
            //$output->writeTimedLog($pass->getDescription(), array($pass, 'execute'), array($projectDescriptor));
        }

//        if ($progress) {
//            $progress->finish();
//        }

        return 0;
    }

    /**
     * Returns the Cache.
     */
    private function getCache(): StorageInterface
    {
        return $this->cache;
    }

    /**
     * Connect a series of output messages to various events to display progress.
     */
    private function connectOutputToEvents()
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
                $this->logger->info('  Initialize writer "' . get_class($event->getWriter()) . '"');
            }
        );
        Dispatcher::getInstance()->addListener(
            Transformer::EVENT_PRE_TRANSFORMATION,
            function (PreTransformationEvent $event) {
                $this->logger->info(
                    '  Execute transformation using writer "' . $event->getTransformation()->getWriter() . '"'
                );
            }
        );
    }
}
