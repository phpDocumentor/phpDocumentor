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

use League\Tactician\CommandBus;
use phpDocumentor\Application\Commands\CacheProject;
use phpDocumentor\Application\Commands\DumpAstToDisk;
use phpDocumentor\Application\Commands\InitializeParser;
use phpDocumentor\Application\Commands\LoadProjectFromCache;
use phpDocumentor\Application\Commands\LoadTemplates;
use phpDocumentor\Application\Commands\MergeConfigurationWithCommandLineOptions;
use phpDocumentor\Application\Commands\ParseFiles;
use phpDocumentor\Application\Commands\Transform;
use phpDocumentor\Configuration;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\Validator\Error;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Backend\Php;
use phpDocumentor\Parser\Event\PreFileEvent;
use phpDocumentor\Parser\Parser;
use phpDocumentor\Transformer\Event\PreTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Event\WriterInitializationEvent;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformer;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

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
    /** @var Configuration */
    private $configuration;

    /** @var Parser $parser */
    private $parser;

    /** @var Transformer $transformer Principal object for guiding the transformation process */
    private $transformer;

    /** @var Dispatcher */
    private $dispatcher;

    /** @var CommandBus */
    private $commandBus;

    /**
     * Initializes the command with all necessary dependencies
     *
     * @param Configuration  $configuration
     * @param Parser         $parser
     * @param Transformer    $transformer
     * @param Dispatcher     $dispatcher
     * @param CommandBus     $commandBus
     */
    public function __construct(
        Configuration $configuration,
        Parser $parser,
        Transformer $transformer,
        Dispatcher $dispatcher,
        CommandBus $commandBus
    ) {
        $this->configuration = $configuration;
        $this->parser        = $parser;
        $this->transformer   = $transformer;
        $this->dispatcher    = $dispatcher;
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
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->commandBus->handle(
            new MergeConfigurationWithCommandLineOptions(
                $this->configuration,
                $input->getOptions(),
                $input->getArguments()
            )
        );

        $target = (string)$this->configuration->getParser()->getTarget();
        $cacheFolder = $input->getOption('cache-folder') ?: $target;
        if (file_exists($cacheFolder)) {
            $this->commandBus->handle(new LoadProjectFromCache($cacheFolder));
        }

        $this->commandBus->handle(new InitializeParser($this->configuration));

        $progress = $this->getProgressBar($input);
        $this->connectOutputToEvents($progress, $output);

        $this->startProgressbar($progress, $output, $this->parser->getFiles()->count());
        $this->commandBus->handle(new ParseFiles($this->configuration));
        $this->finishProgressbar($progress);

        $this->commandBus->handle(new CacheProject($cacheFolder));
        $this->commandBus->handle(new LoadTemplates($input->getOption('template'), $this->configuration));

        $this->startProgressbar($progress, $output, count($this->transformer->getTemplates()->getTransformations()));
        $this->commandBus->handle(new Transform($target));
        $this->finishProgressbar($progress);

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_DEBUG) {
            $this->commandBus->handle(new DumpAstToDisk('ast.dump'));
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
    private function startProgressbar($progress, OutputInterface $output, $numberOfFiles)
    {
        if (! $progress) {
            return;
        }

        $progress->start($output, $numberOfFiles);
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
     * Connect a series of output messages to various events to display progress.
     *
     * @param OutputInterface $output
     *
     * @return void
     */
    private function connectOutputToEvents($progress, OutputInterface $output)
    {
        if ($progress) {
            $this->dispatcher->addListener(
                Parser::EVENT_PARSE_FILE_BEFORE,
                function () use ($progress) {
                    $progress->advance();
                }
            );

            $this->dispatcher->addListener(
                Transformer::EVENT_POST_TRANSFORMATION,
                function () use ($progress) {
                    $progress->advance();
                }
            );
        } else {
            Dispatcher::getInstance()->addListener(
                Parser::EVENT_PARSE_FILE_BEFORE,
                function (PreFileEvent $event) use ($output) {
                    $output->writeln(sprintf('  Parsing <comment>%s</comment>', $event->getFile()));
                }
            );
            Dispatcher::getInstance()->addListener(
                Php::EVENT_ANALYZED_FILE,
                function (GenericEvent $event) use ($output) {
                    /** @var FileDescriptor $descriptor */
                    $descriptor = $event->getSubject();
                    /** @var Error $error */
                    foreach ($descriptor->getAllErrors() as $error) {
                        $output->writeln(
                            '  <error> ' . vsprintf($error->getCode(), $error->getContext()) . ' </error>'
                        );
                    }
                }
            );
            Dispatcher::getInstance()->addListener(
                Parser::EVENT_FILES_COLLECTED,
                function (GenericEvent $event) use ($output) {
                    $files = $event->getSubject();
                    $output->writeln(sprintf("\nFound <info>%d</info> files", count($files)));
                }
            );
            Dispatcher::getInstance()->addListener(
                Transformer::EVENT_PRE_TRANSFORM,
                function (PreTransformEvent $event) use ($output) {
                    $transformations = $event->getSubject()->getTemplates()->getTransformations();
                    $output->writeln(sprintf("\nApplying <info>%d</info> transformations", count($transformations)));
                }
            );
            Dispatcher::getInstance()->addListener(
                Transformer::EVENT_PRE_INITIALIZATION,
                function (WriterInitializationEvent $event) use ($output) {
                    $output->writeln('  Initialize writer <comment>' . get_class($event->getWriter()) . '</comment>');
                }
            );
            Dispatcher::getInstance()->addListener(
                Transformer::EVENT_PRE_TRANSFORMATION,
                function (PreTransformationEvent $event) use ($output) {
                    $output->writeln(
                        '  Execute transformation using writer <comment>'
                        . $event->getTransformation()->getWriter() . '</comment>'
                    );
                }
            );
        }
    }
}
