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

namespace phpDocumentor\Command\Project;

use phpDocumentor\Command\Command;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use Symfony\Component\Console\Input\ArrayInput;
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
        $parse_command     = $this->getApplication()->find('project:parse');
        $transform_command = $this->getApplication()->find('project:transform');

        $parse_input = new ArrayInput(
            array(
                 'command'              => 'project:parse',
                 '--filename'           => $input->getOption('filename'),
                 '--directory'          => $input->getOption('directory'),
                 '--encoding'           => $input->getOption('encoding'),
                 '--extensions'         => $input->getOption('extensions'),
                 '--ignore'             => $input->getOption('ignore'),
                 '--ignore-tags'        => $input->getOption('ignore-tags'),
                 '--hidden'             => $input->getOption('hidden'),
                 '--ignore-symlinks'    => $input->getOption('ignore-symlinks'),
                 '--markers'            => $input->getOption('markers'),
                 '--title'              => $input->getOption('title'),
                 '--target'             => $input->getOption('cache-folder') ?: $input->getOption('target'),
                 '--force'              => $input->getOption('force'),
                 '--validate'           => $input->getOption('validate'),
                 '--visibility'         => $input->getOption('visibility'),
                 '--defaultpackagename' => $input->getOption('defaultpackagename'),
                 '--sourcecode'         => $input->getOption('sourcecode'),
                 '--parseprivate'       => $input->getOption('parseprivate'),
                 '--progressbar'        => $input->getOption('progressbar'),
                 '--log'                => $input->getOption('log')
            ),
            $this->getDefinition()
        );

        $return_code = $parse_command->run($parse_input, $output);
        if ($return_code !== 0) {
            return $return_code;
        }

        $transform_input = new ArrayInput(
            array(
                 'command'       => 'project:transform',
                 '--source'      => $input->getOption('cache-folder') ?: $input->getOption('target'),
                 '--target'      => $input->getOption('target'),
                 '--template'    => $input->getOption('template'),
                 '--progressbar' => $input->getOption('progressbar'),
                 '--log'         => $input->getOption('log')
            )
        );
        $return_code = $transform_command->run($transform_input, $output);
        if ($return_code !== 0) {
            return $return_code;
        }

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_DEBUG) {
            /** @var ProjectDescriptorBuilder $descriptorBuilder */
            $descriptorBuilder = $this->getService('descriptor.builder');
            file_put_contents('ast.dump', serialize($descriptorBuilder->getProjectDescriptor()));
        }

        return 0;
    }
}
