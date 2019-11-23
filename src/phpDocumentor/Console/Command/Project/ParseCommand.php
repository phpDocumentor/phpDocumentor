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

use League\Pipeline\PipelineInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Parses the given source code and creates a structure file.
 *
 * The parse task uses the source files defined either by -f or -d options and
 * generates a structure file (structure.xml) at the target location (which is
 * the folder 'output' unless the option -t is provided).
 */
final class ParseCommand extends Command
{
    /** @var PipelineInterface */
    private $pipeline;

    public function __construct(PipelineInterface $pipeline)
    {
        $this->pipeline = $pipeline;

        parent::__construct('project:parse');
    }

    /**
     * Initializes this command and sets the name, description, options and arguments.
     */
    protected function configure(): void
    {
        $this->setAliases(['parse'])
            ->setDescription('Creates a structure file from your source code')
            ->setHelp(<<<HELP
The parse task uses the source files defined either by -f or -d options and
generates cache files at the target location.
HELP
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
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of extensions to parse, defaults to php, php3 and phtml'
            )
            ->addOption(
                'ignore',
                'i',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of file(s) and directories that will be ignored. Wildcards * and ? '
                . 'are supported'
            )
            ->addOption(
                'ignore-tags',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of tags that will be ignored, defaults to none. package, subpackage '
                . 'and ignore may not be ignored.'
            )
            ->addOption(
                'hidden',
                null,
                InputOption::VALUE_NONE,
                'Use this option to tell phpDocumentor to parse files and directories that begin with a '
                . 'period (.), by default these are ignored'
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
                'Comma-separated list of markers/tags to filter',
                ['TODO', 'FIXME']
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
                'Specifies the parse visibility that should be displayed in the documentation (comma '
                . 'separated e.g. "public,protected")'
            )
            ->addOption(
                'sourcecode',
                null,
                InputOption::VALUE_NONE,
                'Whether to include syntax highlighted source code'
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
            );

        parent::configure();
    }

    /**
     * Executes the business logic involved with this command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pipeLine = $this->pipeline;
        $pipeLine($input->getOptions());

        return 0;
    }
}
