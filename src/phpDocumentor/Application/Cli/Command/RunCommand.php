<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */


namespace phpDocumentor\Application\Cli\Command;

use Desarrolla2\Cache\Adapter\AdapterInterface;
use Desarrolla2\Cache\Adapter\File;
use Desarrolla2\Cache\CacheInterface;
use phpDocumentor\Application\Cli\Command\Helper\ConfigurationHelper;
use League\Tactician\CommandBus;
use phpDocumentor\Application\Commands\InitializeParser;
use phpDocumentor\Application\Commands\MergeConfigurationWithCommandLineOptions;
use phpDocumentor\Application\Commands\ParseFiles;
use phpDocumentor\Command\Command;
use phpDocumentor\Command\Helper\ConfigurationHelper;
use phpDocumentor\Compiler\Compiler;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Configuration;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\Example\Finder;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Parser;
use phpDocumentor\Transformer\Event\PreTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Event\WriterInitializationEvent;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Parse and transform the given directory (-d|-f) to the given location (-t).
 *
 * phpDocumentor creates documentation from PHP source files. The simplest way
 * to use it is:
 *
 *     $ phpdoc run -d <directory to parse> -t <output directory>
 *
 * This will parse every file ending with .php, .php3 and .phtml in <directory
 * to parse> and then output a HTML site containing easily readable documentation
 * in <output directory>.
 *
 * phpDocumentor will try to look for a phpdoc.dist.xml or phpdoc.xml file in your
 * current working directory and use that to override the default settings if
 * present. In the configuration file can you specify the same settings (and
 * more) as the command line provides.
 */
final class RunCommand extends Command
{
    /** @var Analyzer */
    private $analyzer;

    /** @var Parser $parser */
    private $parser;

    /** @var Transformer $transformer Principal object for guiding the transformation process */
    private $transformer;

    /** @var Compiler $compiler Collection of pre-transformation actions (Compiler Passes) */
    private $compiler;

    /** @var Dispatcher */
    private $dispatcher;

    /** @var CacheInterface */
    private $cache;

    /** @var CommandBus */
    private $commandBus;

    /**
     * Initializes the command with all necessary dependencies
     *
     * @param Analyzer $analyzer
     * @param Parser $parser
     * @param Transformer $transformer
     * @param Compiler $compiler
     * @param Dispatcher $dispatcher
     * @param CacheInterface $cache
     * @param CommandBus $commandBus
     */
    public function __construct(
        Analyzer $analyzer,
        Parser $parser,
        Transformer $transformer,
        Compiler $compiler,
        Dispatcher $dispatcher,
        CacheInterface $cache,
        CommandBus $commandBus
    ) {
        $this->analyzer      = $analyzer;
        $this->parser        = $parser;
        $this->transformer   = $transformer;
        $this->compiler      = $compiler;
        $this->dispatcher    = $dispatcher;
        $this->cache         = $cache;
        $this->commandBus    = $commandBus;

        parent::__construct();
    }

    /**
     * Initializes this command and sets the name, description, options and
     * arguments.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('project:run')
            ->setAliases(array('run'))
            ->setDescription(
                'Parses and transforms the given files to a specified location'
            )
            ->setHelp(
<<<HELP
phpDocumentor creates documentation from PHP source files. The simplest way
to use it is:

    <info>$ phpdoc run -d [directory to parse] -t [output directory]</info>

This will parse every file ending with .php, .php3 and .phtml in <directory
to parse> and then output a HTML site containing easily readable documentation
in <output directory>.

phpDocumentor will try to look for a phpdoc.dist.xml or phpdoc.xml file in your
current working directory and use that to override the default settings if
present. In the configuration file can you specify the same settings (and
more) as the command line provides.

<comment>Other commands</comment>
In addition to this command phpDocumentor also supports additional commands:

<comment>Available commands:</comment>
<info>  help
  list
  parse
  run
  transform
<comment>project</comment>
  project:parse
  project:run
  project:transform
<comment>template</comment>
  template:generate
  template:list
  template:package</info>

You can get a more detailed listing of the commands using the <info>list</info>
command and get help by prepending the word <info>help</info> to the command
name.
HELP
            )
            ->addOption(
                'target',
                't',
                InputOption::VALUE_OPTIONAL,
                'Path where to store the generated output'
            )
            ->addOption(
                'cache-folder',
                null,
                InputOption::VALUE_OPTIONAL,
                'Path where to store the cache files'
            )
            ->addOption(
                'filename',
                'f',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of files to parse. The wildcards ? and * are supported'
            )
            ->addOption(
                'directory',
                'd',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of directories to (recursively) parse'
            )
            ->addOption(
                'encoding',
                null,
                InputOption::VALUE_OPTIONAL,
                'encoding to be used to interpret source files with'
            )
            ->addOption(
                'extensions',
                'e',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of extensions to parse, defaults to php, php3 and phtml'
            )
            ->addOption(
                'ignore',
                'i',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of file(s) and directories (relative to the source-code directory) that will be '
                . 'ignored. Wildcards * and ? are supported'
            )
            ->addOption(
                'ignore-hidden',
                null,
                InputOption::VALUE_NONE,
                'Use this option to tell phpDocumentor to parse files and directories that begin with a period (.), '
                . 'by default these are ignored'
            )
            ->addOption(
                'ignore-symlinks',
                null,
                InputOption::VALUE_NONE,
                'Ignore symlinks to other files or directories, default is on'
            )
            ->addOption(
                'markers',
                'm',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of markers/tags to filter'
            )
            ->addOption(
                'title',
                null,
                InputOption::VALUE_OPTIONAL,
                'Sets the title for this project; default is the phpDocumentor logo'
            )
            ->addOption(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Forces a full build of the documentation, does not increment existing documentation'
            )
            ->addOption(
                'visibility',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Specifies the parse visibility that should be displayed in the documentation (comma separated e.g. '
                . '"public,protected")'
            )
            ->addOption(
                'defaultpackagename',
                null,
                InputOption::VALUE_OPTIONAL,
                'Name to use for the default package.',
                'Default'
            )
            ->addOption(
                'sourcecode',
                null,
                InputOption::VALUE_NONE,
                'Whether to include syntax highlighted source code'
            )
            ->addOption(
                'progressbar',
                'p',
                InputOption::VALUE_NONE,
                'Whether to show a progress bar; will automatically quiet logging to stdout'
            )
            ->addOption(
                'template',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Name of the template to use (optional)'
            )
            ->addOption(
                'parseprivate',
                null,
                InputOption::VALUE_NONE,
                'Whether to parse DocBlocks marked with @internal tag'
            )
            ->addArgument(
                'paths',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'One or more files and folders to process',
                array()
            );

        parent::configure();
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->commandBus->handle(new MergeConfigurationWithCommandLineOptions($input->getOptions()));
        $this->commandBus->handle(new InitializeParser());

        $progress = $this->startProgressbar($input, $output, $this->parser->getFiles()->count());
        $this->commandBus->handle(new ParseFiles());
        $this->finishProgressbar($progress);

        $transform_input = new ArrayInput(
            array(
                 'command'         => 'project:transform',
                 '--cache-folder'  => $input->getOption('cache-folder') ?: $input->getOption('target'),
                 '--target'        => $input->getOption('target'),
                 '--template'      => $input->getOption('template'),
                 '--progressbar'   => $input->getOption('progressbar'),
            ),
            $this->getDefinition()
        );

        $return_code = $this->transformCommand($transform_input, $output);
        if ($return_code !== 0) {
            return $return_code;
        }

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_DEBUG) {
            file_put_contents('ast.dump', serialize($this->analyzer->getProjectDescriptor()));
        }

        return 0;
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
    private function transformCommand(InputInterface $input, OutputInterface $output)
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

        $source = realpath($configurationHelper->getOption($input, 'cache-folder', 'parser/target'));
        if (!file_exists($source) || !is_dir($source)) {
            throw new \Exception('Invalid source location provided, a path to an existing folder was expected');
        }

        $this->getCache()->setAdapter(new File($source));

        $projectDescriptor = $this->getAnalyzer()->getProjectDescriptor();
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
     * Initializes the progress bar component and register a listener that will increment the progressbar.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param integer         $numberOfFiles
     *
     * @return ProgressHelper
     */
    private function startProgressbar(InputInterface $input, OutputInterface $output, $numberOfFiles)
    {
        /** @var ProgressHelper $progress */
        $progress = $this->getProgressBar($input);
        if (!$progress) {
            $this->getHelper('phpdocumentor_logger')->connectOutputToLogging($output, $this);
        }

        if ($progress) {
            $this->getEventDispatcher()->addListener(
                'parser.file.pre',
                function () use ($progress) {
                    $progress->advance();
                }
            );

            $progress->start($output, $numberOfFiles);
        }

        return $progress;
    }

    /**
     * Finalizes the progress bar after all handling is complete.
     *
     * @param ProgressHelper|null $progress
     *
     * @return void
     */
    private function finishProgressbar($progress)
    {
        if (! $progress) {
            return;
        }

        $progress->finish();
    }

    /**
     * Returns the Event Dispatcher.
     *
     * @return EventDispatcherInterface|null
     */
    private function getEventDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Returns the analyzer object containing the AST and other meta-data.
     *
     * @return Analyzer
     */
    public function getAnalyzer()
    {
        return $this->analyzer;
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
     * Returns the Cache.
     *
     * @return CacheInterface
     */
    private function getCache()
    {
        return $this->cache;
    }

    /**
     * Retrieves the templates to be used by analyzing the options and the configuration.
     *
     * @param InputInterface $input
     *
     * @return string[]
     */
    private function getTemplates(InputInterface $input)
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
    private function createTransformation(array $transformations)
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
    private function appendReceivedTransformations(Transformer $transformer, $received)
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

        $this->getEventDispatcher()->addListener(
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
