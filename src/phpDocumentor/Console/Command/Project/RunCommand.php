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

namespace phpDocumentor\Console\Command\Project;

use League\Pipeline\PipelineInterface;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Event\PreParsingEvent;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Transformer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

use function count;
use function file_put_contents;
use function floor;
use function round;
use function serialize;
use function sprintf;

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
class RunCommand extends Command
{
    /** @var ProjectDescriptorBuilder */
    private $projectDescriptorBuilder;

    /** @var PipelineInterface */
    private $pipeline;

    /** @var ProgressBar */
    private $progressBar;

    /** @var ProgressBar */
    private $transformerProgressBar;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        ProjectDescriptorBuilder $projectDescriptorBuilder,
        PipelineInterface $pipeline,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct('project:run');

        $this->projectDescriptorBuilder = $projectDescriptorBuilder;
        $this->pipeline = $pipeline;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Initializes this command and sets the name, description, options and
     * arguments.
     */
    protected function configure(): void
    {
        $this->setName('project:run')
            ->setAliases(['run'])
            ->setDescription(
                'Parses and transforms the given files to a specified location'
            )
            ->setHelp(
                <<<HELP
                phpDocumentor creates documentation from PHP source files. The simplest way
                to use it is:

                    <info>$ phpdoc run -d [directory to parse] -t [output directory]</info>

                This will parse every file ending with .php in <directory
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
                  run
                <comment>project</comment>
                  project:run
                </info>

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
                'File to parse, glob patterns are supported. Provide multiple options of this type to add
                multiple files.'
            )
            ->addOption(
                'directory',
                'd',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'directory to parse, glob patterns are supported. Provide multiple options of this type to add
                multiple directories.'
            )
            ->addOption(
                'encoding',
                null,
                InputOption::VALUE_OPTIONAL,
                'encoding to be used to interpret source files with'
            )
            ->addOption(
                'extensions',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Provide multiple options of this type to add multiple extensions. default is php'
            )
            ->addOption(
                'ignore',
                'i',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'File(s) and directories (relative to the source-code directory) that will be '
                . 'ignored. Glob patterns are supported. Add multiple options of this type of add more ignore patterns'
            )
            ->addOption(
                'ignore-tags',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Tag that will be ignored, defaults to none. package, subpackage and ignore '
                . 'may not be ignored. Add multiple options of this type to ignore multiple tags.'
            )
            ->addOption(
                'hidden',
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
                'validate',
                null,
                InputOption::VALUE_NONE,
                'Validates every processed file using PHP Lint, costs a lot of performance'
            )
            ->addOption(
                'visibility',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Specifies the parse visibility that should be displayed in the documentation. Add multiple options of
                this type to specify multiple levels.'
                . '("public,protected")'
            )
            ->addOption(
                'defaultpackagename',
                null,
                InputOption::VALUE_OPTIONAL,
                'Name to use for the default package.'
            )
            ->addOption(
                'sourcecode',
                null,
                InputOption::VALUE_NONE,
                'Whether to include syntax highlighted source code'
            )
            ->addOption(
                'template',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Name of the template to use (optional)'
            )
            ->addOption(
                'examples-dir',
                null,
                InputOption::VALUE_OPTIONAL,
                'Directory to seacher for example files referenced by @example tags'
            )
            ->addOption(
                'setting',
                's',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Provide custom setting(s) as "key=value", run again with <info>--list-settings</info> for a list'
            )
            ->addOption(
                'list-settings',
                null,
                InputOption::VALUE_NONE,
                'Returns a list of available settings'
            )
            ->addOption(
                'parseprivate',
                null,
                InputOption::VALUE_NONE,
                'Whether to parse DocBlocks marked with @internal tag'
            );

        parent::configure();
    }

    /**
     * Executes the business logic involved with this command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $event = $stopwatch->start('all');

        $output->writeln('phpDocumentor ' . $this->getApplication()->getVersion());
        $output->writeln('');

        if ($input->getOption('list-settings')) {
            return ($this->getApplication()->find('settings:list'))
                ->run(new ArrayInput([]), $output);
        }

        $this->observeProgressToShowProgressBars($output);

        $pipeLine = $this->pipeline;
        $pipeLine($input->getOptions());

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_DEBUG) {
            file_put_contents('ast.dump', serialize($this->projectDescriptorBuilder->getProjectDescriptor()));
        }

        $event->stop();
        $output->writeln('');

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(sprintf('Observed max. memory usage: %s mb', round($event->getMemory() / 1024 / 1024, 2)));
        }

        $output->writeln(sprintf('All done in %s!', $this->durationInText($event)));

        return 0;
    }

    private function observeProgressToShowProgressBars(OutputInterface $output): void
    {
        // Code that is poorly testable and not worth the effort
        // @codeCoverageIgnoreStart
        if ($output->getVerbosity() !== OutputInterface::VERBOSITY_NORMAL) {
            return;
        }

        $dispatcherInstance = Dispatcher::getInstance();
        $dispatcherInstance->addListener(
            'parser.pre',
            function (PreParsingEvent $event) use ($output): void {
                $output->writeln('Parsing files');
                $this->progressBar = new ProgressBar($output, $event->getFileCount());
            }
        );
        $dispatcherInstance->addListener(
            'parser.file.pre',
            function (): void {
                $this->progressBar->advance();
            }
        );

        $dispatcherInstance->addListener(
            Transformer::EVENT_PRE_TRANSFORM,
            function (PreTransformEvent $event) use ($output): void {
                $output->writeln('');
                $output->writeln('Applying transformations (can take a while)');
                $this->transformerProgressBar = new ProgressBar(
                    $output,
                    count($event->getTransformations())
                );
            }
        );

        $this->eventDispatcher->addListener(
            Transformer::EVENT_POST_TRANSFORMATION,
            function (): void {
                $this->transformerProgressBar->advance();
            }
        );
        // @codeCoverageIgnoreEnd
    }

    private function durationInText(StopwatchEvent $event): string
    {
        $durationText = '';
        $duration = round($event->getDuration() / 1000);
        if ($duration > 59) {
            $minutes = floor($duration / 60);
            $durationText .= sprintf('%s minute%s ', $minutes, $minutes > 1 ? 's' : '');
        }

        $durationText .= ($duration % 60) . ' seconds';

        return $durationText;
    }
}
