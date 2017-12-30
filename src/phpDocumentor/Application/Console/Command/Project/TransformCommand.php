<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Console\Command\Project;

use phpDocumentor\Application\Console\Command\Command;
use phpDocumentor\Application\Console\Command\Helper\ConfigurationHelper;
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
use Symfony\Component\Console\Helper\ProgressHelper;
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
     */
    public function __construct(ProjectDescriptorBuilder $builder, Transformer $transformer, Compiler $compiler)
    {
        parent::__construct('project:transform');

        $this->builder = $builder;
        $this->transformer = $transformer;
        $this->compiler = $compiler;
    }

    /**
     * Initializes this command and sets the name, description, options and
     * arguments.
     */
    protected function configure(): void
    {
        $this->setAliases(['transform'])
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
     */
    public function getBuilder(): ProjectDescriptorBuilder
    {
        return $this->builder;
    }

    /**
     * Returns the transformer used to guide the transformation process from AST to output.
     */
    public function getTransformer(): Transformer
    {
        return $this->transformer;
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @throws \Exception if the provided source is not an existing file or a folder.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var ConfigurationHelper $configurationHelper */
        $configurationHelper = $this->getHelper('phpdocumentor_configuration');

        /** @var ProgressHelper $progress */
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
        $this->writeTimedLog($output,'Load cache', [$mapper, 'populate'], [$projectDescriptor]);

        foreach ($this->getTemplates($input) as $template) {
            $this->writeTimedLog(
                $output,
                'Preparing template "' . $template . '"',
                [$transformer->getTemplates(), 'load'],
                [$template, $transformer]
            );
        }

        $this->writeTimedLog(
            $output,
            'Preparing ' . count($transformer->getTemplates()->getTransformations()) . ' transformations',
            [$this, 'loadTransformations'],
            [$transformer]
        );

        if ($progress) {
            $progress->start($output, count($transformer->getTemplates()->getTransformations()));
        }

        /** @var CompilerPassInterface $pass */
        foreach ($this->compiler as $pass) {
            if ($pass === null) {
                $output->writeln('<error>Invalid compiler pass found</error>');
                continue;
            }
            $this->writeTimedLog($output, $pass->getDescription(), [$pass, 'execute'], [$projectDescriptor]);
        }

        if ($progress) {
            $progress->finish();
        }

        return 0;
    }

    /**
     * Returns the Cache.
     * @throws \Pimple\Exception\UnknownIdentifierException
     */
    protected function getCache(): StorageInterface
    {
        return $this->getContainer()->get('descriptor.cache');
    }

    /**
     * Retrieves the templates to be used by analyzing the options and the configuration.
     *
     * @return string[]
     */
    protected function getTemplates(InputInterface $input): array
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
            $templates = ['clean'];
        }

        // Support template entries that contain multiple templates using a comma separated list
        // like checkstyle,clean
        foreach ($templates as $key => $template) {
            $commaSeparatedTemplates = explode(',', $template);
            if (count($commaSeparatedTemplates) > 1) {
                // replace the current item with the first in the list
                $templates[$key] = trim(array_shift($commaSeparatedTemplates));
                // append all additional templates to the list of templates
                foreach ($commaSeparatedTemplates as $subtemplate) {
                    $templates[] = $subtemplate;
                }
            }
        }

        return $templates;
    }

    /**
     * Load custom defined transformations.
     *
     * @todo this is an ugly implementation done for speed of development, should be refactored
     */
    public function loadTransformations(Transformer $transformer)
    {
        /** @var ConfigurationHelper $configurationHelper */
        $configurationHelper = $this->getHelper('phpdocumentor_configuration');

        $received = [];
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
     */
    protected function createTransformation(array $transformations): Transformation
    {
        return new Transformation(
            $transformations['query'] ?? '',
            $transformations['writer'],
            $transformations['source'] ?? '',
            $transformations['artifact'] ?? ''
        );
    }

    /**
     * Append received transformations.
     */
    protected function appendReceivedTransformations(Transformer $transformer, array $received)
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
     */
    private function connectOutputToEvents(OutputInterface $output)
    {
        $this->getHelper('phpdocumentor_logger')->connectOutputToLogging($output, $this);

        Dispatcher::getInstance()->addListener(
            Transformer::EVENT_PRE_TRANSFORM,
            function (PreTransformEvent $event) use ($output) {
                /** @var Transformer $transformer */
                $transformer = $event->getSubject();
                $templates = $transformer->getTemplates();
                $transformations = $templates->getTransformations();
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
