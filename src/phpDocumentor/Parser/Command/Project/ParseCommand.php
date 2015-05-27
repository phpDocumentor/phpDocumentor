<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Command\Project;

use phpDocumentor\Command\Command;
use phpDocumentor\Configuration;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\Example\Finder;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerChain;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Parser;
use phpDocumentor\Partials\Collection;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Parses the given source code and creates a structure file.
 *
 * The parse task uses the source files defined either by -f or -d options and generates a structure file
 * (structure.xml) at the target location (which is the folder 'output' unless the option -t is provided).
 */
final class ParseCommand extends Command
{
    /** @var Parser $parser */
    protected $parser;

    /** @var Finder $exampleFinder */
    private $exampleFinder;

    /**
     * Evil!
     *
     * Because we need to configuration from the container but cannot inject the configuration because it needs to be
     * postponed as late as possible, later we should find a way to remove this dependency.
     *
     * @todo fight the evil.
     *
     * @var \DI\Container
     */
    private $container;

    /**
     * Initializes this command with the dependencies used to parse files.
     *
     * @param Parser              $parser
     * @param Finder              $exampleFinder
     */
    public function __construct(
        Parser $parser,
        Finder $exampleFinder,
        \DI\Container $container
    ) {
        $this->parser        = $parser;
        $this->exampleFinder = $exampleFinder;
        $this->container     = $container;

        parent::__construct('project:parse');
    }

    /**
     * Initializes this command and sets the name, description, options and arguments.
     *
     * @return void
     */
    protected function configure()
    {
        // minimization of the following expression
        $this->setAliases(array('parse'))
            ->setDescription('Creates a structure file from your source code')
            ->setHelp(
                'The parse task uses the source files defined either by -f or -d options and generates cache '
                . 'files at the target location.'
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
                'target',
                't',
                InputOption::VALUE_OPTIONAL,
                'Path where to store the cache (optional)'
            )
            ->addOption(
                'encoding',
                null,
                InputOption::VALUE_OPTIONAL,
                'Encoding to be used to interpret source files with'
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
                'Comma-separated list of file(s) and directories that will be ignored. Wildcards * and ? are supported'
            )
            ->addOption(
                'ignore-tags',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of tags that will be ignored, defaults to none. package, subpackage and ignore '
                . 'may not be ignored.'
            )
            ->addOption(
                'ignore-hidden',
                null,
                InputOption::VALUE_NONE,
                'Use this option to tell phpDocumentor whether to ignore files and directories that begin with a '
                . 'period (.), by default this is true and thus these are ignored'
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
                'parseprivate',
                null,
                InputOption::VALUE_NONE,
                'Whether to parse DocBlocks marked with @internal tag'
            )
            ->addOption(
                'defaultpackagename',
                null,
                InputOption::VALUE_OPTIONAL,
                'Name to use for the default package.',
                'Default'
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
     * Overwrites the loaded configuration with any of the command line options, boots the parser and analyzes each file
     * provided using the `-t` or `-d` argument.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configuration = $this->populateConfiguration($input);
        $this->container->get(InitializerChain::class)->initialize($this->container->get(Analyzer::class));
        $this->parser->boot($configuration->getParser());
        $this->configureExampleFinder($configuration);

        $progress = $this->startProgressbar($input, $output, $this->parser->getFiles()->count());
        $this->parse($configuration);
        $this->finishProgressbar($progress);

        return 0;
    }

    /**
     * For each given option in this command we (over)write a section of the configuration that matches that option.
     *
     * @param InputInterface $input
     *
     * @return Configuration
     */
    private function populateConfiguration(InputInterface $input)
    {
        $configuration = $this->getConfiguration();

        $this->overwriteConfigurationSetting($input, $configuration->getFiles(), 'filename', 'Files');
        $this->overwriteConfigurationSetting($input, $configuration->getFiles(), 'directory', 'Directories');
        $this->overwriteConfigurationSetting($input, $configuration->getParser(), 'target');
        $this->overwriteConfigurationSetting($input, $configuration->getParser(), 'encoding');
        $this->overwriteConfigurationSetting($input, $configuration->getParser(), 'extensions');
        $this->overwriteConfigurationSetting($input, $configuration->getFiles(), 'ignore');
        $this->overwriteConfigurationSetting($input, $configuration->getFiles(), 'ignore-hidden', 'IgnoreHidden');
        $this->overwriteConfigurationSetting($input, $configuration->getFiles(), 'ignore-symlinks', 'IgnoreSymlinks');
        $this->overwriteConfigurationSetting($input, $configuration->getParser(), 'markers');
        $this->overwriteConfigurationSetting($input, $configuration, 'title');
        $this->overwriteConfigurationSetting($input, $configuration->getParser(), 'force', 'ShouldRebuildCache');
        $this->overwriteConfigurationSetting(
            $input,
            $configuration->getParser(),
            'defaultpackagename',
            'DefaultPackageName'
        );

        $this->overwriteConfigurationSetting($input, $configuration->getParser(), 'visibility');
        if ($input->getOption('parseprivate')) {
            $configuration->getParser()->setVisibility($configuration->getParser()->getVisibility() . ',internal');
        }
        if (! $configuration->getParser()->getVisibility()) {
            $configuration->getParser()->setVisibility('default');
        }

        // TODO: Add handling of this option
        if ($input->getOption('sourcecode')) {
            // $configuration->getParser()->setMarkers($input->getOption('visibility'));
        }

        $this->fixFilesConfiguration($configuration);

        foreach ($input->getArgument('paths') as $path) {
            $this->addPathToConfiguration($path, $configuration);
        }

        return $configuration;
    }

    /**
     * Overwrites a configuration option with the given option from the input if it was passed.
     *
     * @param InputInterface $input
     * @param object         $section               The configuration (sub)object to modify
     * @param string         $optionName            The name of the option to read from the input.
     * @param string|null    $configurationItemName when omitted the optionName is used where the first letter
     *     is uppercased.
     *
     * @return void
     */
    private function overwriteConfigurationSetting($input, $section, $optionName, $configurationItemName = null)
    {
        if ($configurationItemName === null) {
            $configurationItemName = ucfirst($optionName);
        }

        if ($input->getOption($optionName)) {
            $section->{'set' . $configurationItemName}($input->getOption($optionName));
        }
    }

    /**
     * Configures the paths of the example finder to match the configuration.
     *
     * @param Configuration $configuration
     *
     * @return void
     */
    private function configureExampleFinder(Configuration $configuration)
    {
        $this->exampleFinder->setSourceDirectory($this->parser->getFiles()->getProjectRoot());
        $this->exampleFinder->setExampleDirectories($configuration->getFiles()->getExamples());
    }

    /**
     * Parses the files collected by the parser, stores the title and applies the partials.
     *
     * @param Configuration $configuration
     *
     * @return void
     */
    private function parse(Configuration $configuration)
    {
        $projectDescriptor = $this->parser->parse();
        $projectDescriptor->setName($configuration->getTitle());
        $projectDescriptor->setPartials($this->container->get(Collection::class));
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
     * Returns the configuration for the application.
     *
     * @return Configuration
     */
    private function getConfiguration()
    {
        return $this->container->get(Configuration::class);
    }

    /**
     * Returns the Event Dispatcher.
     *
     * @return EventDispatcherInterface|null
     */
    private function getEventDispatcher()
    {
        return $this->container->get(Dispatcher::class);
    }

    /**
     * The files configuration node has moved, this method provides backwards compatibility for phpDocumentor 3.
     *
     * We add the files configuration because it should actually belong there, simplifies the interface but
     * removing it is a rather serious BC break. By using a non-serialized setter/property in the parser config
     * and setting the files config on it we can simplify this interface.
     *
     * @param Configuration $configuration
     *
     * @deprecated to be removed in phpDocumentor 4
     *
     * @return void
     */
    private function fixFilesConfiguration(Configuration $configuration)
    {
        if (! $configuration->getParser()->getFiles() && $configuration->getFiles()) {
            trigger_error(
                'Your source files and directories should be declared in the "parser" node of your configuration but '
                . 'was found in the root of your configuration. This is deprecated starting with phpDocumentor 3 and '
                . 'will be removed with phpDocumentor 4.',
                E_USER_DEPRECATED
            );

            $configuration->getParser()->setFiles($configuration->getFiles());
            $configuration->setFiles(null);
        }
    }

    /**
     * Adds the given path to the Files or Directories section of the configuration depending on whether it is a file
     * or folder.
     *
     * @param string        $path
     * @param Configuration $configuration
     *
     * @return void
     */
    private function addPathToConfiguration($path, $configuration)
    {
        $fileInfo = new \SplFileInfo($path);
        if ($fileInfo->isDir()) {
            $directories   = $configuration->getParser()->getFiles()->getDirectories();
            $directories[] = $path;
            $configuration->getParser()->getFiles()->setDirectories($directories);
        } else {
            $files   = $configuration->getParser()->getFiles()->getFiles();
            $files[] = $path;
            $configuration->getParser()->getFiles()->setFiles($files);
        }
    }
}
