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

namespace phpDocumentor\Console\Command\Project;

use League\Pipeline\Pipeline;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Event\PreFileEvent;
use phpDocumentor\Parser\Event\PreParsingEvent;
use phpDocumentor\Transformer\Event\PostTransformationEvent;
use phpDocumentor\Transformer\Event\PreTransformEvent;
use phpDocumentor\Transformer\Transformer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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

    /** @var Pipeline */
    private $pipeline;

    /** @var ProgressBar */
    private $progressBar;

    /** @var ProgressBar */
    private $transformerProgressBar;

    /** @var LoggerInterface */
    private $logger;

    /**
     * RunCommand constructor.
     */
    public function __construct(
        ProjectDescriptorBuilder $projectDescriptorBuilder,
        Pipeline $pipeline,
        LoggerInterface $logger
    ) {
        parent::__construct('project:run');

        $this->projectDescriptorBuilder = $projectDescriptorBuilder;
        $this->pipeline = $pipeline;
        $this->logger = $logger;
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
                null,
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
                'ignore-tags',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of tags that will be ignored, defaults to none. package, subpackage and ignore '
                . 'may not be ignored.'
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
            );

        parent::configure();
    }

    /**
     * Executes the business logic involved with this command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('phpDocumentor ' . $this->getApplication()->getVersion());
        $output->writeln('');

        $this->observeProgressToShowProgressBars($output);

        $pipeLine = $this->pipeline;
        $pipeLine($input->getOptions());

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_DEBUG) {
            file_put_contents('ast.dump', serialize($this->projectDescriptorBuilder->getProjectDescriptor()));
        }

        $output->writeln('');
        $output->writeln('All done!');

        return 0;
    }

    private function observeProgressToShowProgressBars(OutputInterface $output): void
    {
        if ($output->getVerbosity() !== OutputInterface::VERBOSITY_NORMAL) {
            return;
        }

        Dispatcher::getInstance()->addListener(
            'parser.pre',
            function (PreParsingEvent $event) use ($output) {
                $output->writeln('Parsing files');
                $this->progressBar = new ProgressBar($output, $event->getFileCount());
            }
        );
        Dispatcher::getInstance()->addListener(
            'parser.file.pre',
            function (PreFileEvent $event) {
                $this->progressBar->advance();
            }
        );
        Dispatcher::getInstance()->addListener(
            Transformer::EVENT_PRE_TRANSFORM,
            function (PreTransformEvent $event) use ($output) {
                $output->writeln('');
                $output->writeln('Applying transformations (can take a while)');
                $this->transformerProgressBar = new ProgressBar(
                    $output,
                    count($event->getSubject()->getTemplates()->getTransformations())
                );
            }
        );
        Dispatcher::getInstance()->addListener(
            Transformer::EVENT_POST_TRANSFORMATION,
            function (PostTransformationEvent $event) {
                $this->transformerProgressBar->advance();
            }
        );
    }
}
