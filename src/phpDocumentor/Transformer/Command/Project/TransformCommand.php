<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Command\Project;

use phpDocumentor\Command\Command;
use phpDocumentor\Command\Helper\ConfigurationHelper;
use phpDocumentor\Compiler\Compiler;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Transformer\Event\PreTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Event\WriterInitializationEvent;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
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
class TransformCommand extends Command
{
    /** @var ProjectDescriptorBuilder $builder Object containing the project meta-data and AST */
    protected $builder;

    /** @var Transformer $transformer Principal object for guiding the transformation process */
    protected $transformer;

    /** @var Compiler $compiler Collection of pre-transformation actions (Compiler Passes) */
    protected $compiler;

    /**
     * Initializes the command with all necessary dependencies to construct human-suitable output from the AST.
     *
     * @param ProjectDescriptorBuilder $builder
     * @param Transformer              $transformer
     * @param Compiler                 $compiler
     */
    public function __construct(ProjectDescriptorBuilder $builder, Transformer $transformer, Compiler $compiler)
    {
        parent::__construct('project:transform');

        $this->builder     = $builder;
        $this->transformer = $transformer;
        $this->compiler    = $compiler;
    }

    /**
     * Initializes this command and sets the name, description, options and
     * arguments.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setAliases(array('transform'))
            ->setDescription(
                'Converts the PHPDocumentor structure file to documentation'
            )
            ->setHelp(
<<<TEXT
This task will execute the transformation rules described in the given
template (defaults to 'responsive') with the given source (defaults to
output/structure.xml) and writes these to the target location (defaults to
'output').

It is possible for the user to receive additional information using the
verbose option or stop additional information using the quiet option. Please
take note that the quiet option also disables logging to file.
TEXT
            );

        $this->addOption(
            'source',
            's',
            InputOption::VALUE_OPTIONAL,
            'Path where the XML source file is located (optional)'
        );
        $this->addOption(
            'target',
            't',
            InputOption::VALUE_OPTIONAL,
            'Path where to store the generated output (optional)'
        );
        $this->addOption(
            'template',
            null,
            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            'Name of the template to use (optional)'
        );
        $this->addOption(
            'progressbar',
            'p',
            InputOption::VALUE_NONE,
            'Whether to show a progress bar; will automatically quiet logging to stdout'
        );

        parent::configure();
    }

    /**
     * Returns the builder object containing the AST and other meta-data.
     *
     * @return ProjectDescriptorBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * Returns the transformer used to guide the transformation process from AST to output.
     *
     * @return Transformer
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception if the provided source is not an existing file or a folder.
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ConfigurationHelper $configurationHelper */
        $configurationHelper = $this->getHelper('phpdocumentor_configuration');

        $progress = $this->getProgressBar($input);
        if (! $progress) {
            $this->connectOutputToEvents($output);
        }

        // initialize transformer
        $transformer = $this->getTransformer();

        $target = (string) $configurationHelper->getOption($input, 'target', 'transformer/target');
        $fileSystem = new Filesystem();
        if (! $fileSystem->isAbsolutePath($target)) {
            $target = getcwd() . DIRECTORY_SEPARATOR . $target;
        }
        $transformer->setTarget($target);

        $source = realpath($configurationHelper->getOption($input, 'source', 'parser/target'));
        if (!file_exists($source) || !is_dir($source)) {
            throw new \Exception('Invalid source location provided, a path to an existing folder was expected');
        }

        $this->getCache()->getOptions()->setCacheDir($source);

        $projectDescriptor = $this->getBuilder()->getProjectDescriptor();
        $mapper = new ProjectDescriptorMapper($this->getCache());
        $output->writeTimedLog('Load cache', array($mapper, 'populate'), array($projectDescriptor));

        foreach ($this->getTemplates($input) as $template) {
            $output->writeTimedLog(
                'Preparing template "'. $template .'"',
                array($transformer->getTemplates(), 'load'),
                array($template, $transformer)
            );
        }
        $output->writeTimedLog(
            'Preparing ' . count($transformer->getTemplates()->getTransformations()) . ' transformations',
            array($this, 'loadTransformations'),
            array($transformer)
        );

        if ($progress) {
            $progress->start($output, count($transformer->getTemplates()->getTransformations()));
        }

        /** @var CompilerPassInterface $pass */
        foreach ($this->compiler as $pass) {
            $output->writeTimedLog($pass->getDescription(), array($pass, 'execute'), array($projectDescriptor));
        }

        if ($progress) {
            $progress->finish();
        }

        return 0;
    }

    /**
     * Returns the Cache.
     *
     * @return StorageInterface
     */
    protected function getCache()
    {
        return $this->getContainer()->offsetGet('descriptor.cache');
    }

    /**
     * Retrieves the templates to be used by analyzing the options and the configuration.
     *
     * @param InputInterface $input
     *
     * @return string[]
     */
    protected function getTemplates(InputInterface $input)
    {
        /** @var ConfigurationHelper $configurationHelper */
        $configurationHelper = $this->getHelper('phpdocumentor_configuration');

        $templates = $input->getOption('template');
        if (!$templates) {
            /** @var Template[] $templatesFromConfig */
            $templatesFromConfig = $configurationHelper->getConfigValueFromPath('transformations/templates');
            foreach ($templatesFromConfig as $template) {
                $templates[] = $template->getName();
            }
        }

        if (!$templates) {
            $templates = array('clean');
        }

        return $templates;
    }

    /**
     * Load custom defined transformations.
     *
     * @param Transformer $transformer
     *
     * @todo this is an ugly implementation done for speed of development, should be refactored
     *
     * @return void
     */
    public function loadTransformations(Transformer $transformer)
    {
        /** @var ConfigurationHelper $configurationHelper */
        $configurationHelper = $this->getHelper('phpdocumentor_configuration');

        $received = array();
        $transformations = $configurationHelper->getConfigValueFromPath('transformations/transformations');
        if (is_array($transformations)) {
            if (isset($transformations['writer'])) {
                $received[] = $this->createTransformation($transformations);
            } else {
                foreach ($transformations as $transformation) {
                    if (is_array($transformation)) {
                        $received[] = $this->createTransformation($transformations);
                    }
                }
            }
        }

        $this->appendReceivedTransformations($transformer, $received);
    }

    /**
     * Create Transformation instance.
     *
     * @param array $transformations
     *
     * @return \phpDocumentor\Transformer\Transformation
     */
    protected function createTransformation(array $transformations)
    {
        return new Transformation(
            isset($transformations['query']) ? $transformations['query'] : '',
            $transformations['writer'],
            isset($transformations['source']) ? $transformations['source'] : '',
            isset($transformations['artifact']) ? $transformations['artifact'] : ''
        );
    }

    /**
     * Append received transformations.
     *
     * @param Transformer $transformer
     * @param array       $received
     *
     * @return void
     */
    protected function appendReceivedTransformations(Transformer $transformer, $received)
    {
        if (!empty($received)) {
            $template = new Template('__');
            foreach ($received as $transformation) {
                $template[] = $transformation;
            }
            $transformer->getTemplates()->append($template);
        }
    }

    /**
     * Adds the transformer.transformation.post event to advance the progressbar.
     *
     * @param InputInterface $input
     *
     * @return HelperInterface|null
     */
    protected function getProgressBar(InputInterface $input)
    {
        $progress = parent::getProgressBar($input);
        if (!$progress) {
            return null;
        }

        /** @var Dispatcher $eventDispatcher */
        $eventDispatcher = $this->getService('event_dispatcher');
        $eventDispatcher->addListener(
            'transformer.transformation.post',
            function () use ($progress) {
                $progress->advance();
            }
        );

        return $progress;
    }

    /**
     * Connect a series of output messages to various events to display progress.
     *
     * @param OutputInterface $output
     *
     * @return void
     */
    private function connectOutputToEvents(OutputInterface $output)
    {
        $this->getHelper('phpdocumentor_logger')->connectOutputToLogging($output, $this);

        Dispatcher::getInstance()->addListener(
            Transformer::EVENT_PRE_TRANSFORM,
            function (PreTransformEvent $event) use ($output) {
                $transformations = $event->getSubject()->getTemplates()->getTransformations();
                $output->writeln(sprintf("\nApplying %d transformations", count($transformations)));
            }
        );
        Dispatcher::getInstance()->addListener(
            Transformer::EVENT_PRE_INITIALIZATION,
            function (WriterInitializationEvent $event) use ($output) {
                $output->writeln('  Initialize writer "' . get_class($event->getWriter()) . '"');
            }
        );
        Dispatcher::getInstance()->addListener(
            Transformer::EVENT_PRE_TRANSFORMATION,
            function (PreTransformationEvent $event) use ($output) {
                $output->writeln(
                    '  Execute transformation using writer "' . $event->getTransformation()->getWriter() . '"'
                );
            }
        );
    }
}
