<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Parser\Command\Project;

use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Cache\Storage\Adapter\AbstractAdapter;
use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Cache\Storage\StorageInterface;
use Zend\Cache\Storage\TaggableInterface;
use phpDocumentor\Command\ConfigurableCommand;
use phpDocumentor\Console\Helper\ProgressHelper;
use phpDocumentor\Descriptor\BuilderAbstract;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Fileset\Collection;
use phpDocumentor\Parser\Event\PreFileEvent;
use phpDocumentor\Parser\Exception\FilesNotFoundException;
use phpDocumentor\Parser\Parser;

/**
 * Parses the given source code and creates a structure file.
 *
 * The parse task uses the source files defined either by -f or -d options and
 * generates a structure file (structure.xml) at the target location (which is
 * the folder 'output' unless the option -t is provided).
 */
class ParseCommand extends ConfigurableCommand
{
    /** @var BuilderAbstract $builder*/
    protected $builder;

    /** @var Parser $parser */
    protected $parser;

    public function __construct($builder, $parser)
    {
        parent::__construct('project:parse');

        $this->builder = $builder;
        $this->parser  = $parser;
    }

    /**
     * @return \phpDocumentor\Descriptor\BuilderAbstract
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return \phpDocumentor\Parser\Parser
     */
    public function getParser()
    {
        return $this->parser;
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
     * Initializes this command and sets the name, description, options and
     * arguments.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setAliases(array('parse'))
            ->setDescription('Creates a structure file from your source code')
            ->setHelp(
<<<HELP
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
                'Comma-separated list of file(s) and directories that will be ignored. Wildcards * and ? are supported'
            )
            ->addOption(
                'ignore-tags',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of tags that will be ignored, defaults to none. package, subpackage and ignore '
                .'may not be ignored.'
            )
            ->addOption(
                'hidden',
                null,
                InputOption::VALUE_NONE,
                'set to on to descend into hidden directories (directories starting with \'.\'), default is on'
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
                array('TODO', 'FIXME')
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
                InputOption::VALUE_OPTIONAL,
                'Specifies the parse visibility that should be displayed in the documentation (comma seperated e.g. '
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
            );

        parent::configure();
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return integer
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // invoke parent to load custom config
        parent::execute($input, $output);

        $builder = $this->getBuilder();
        $projectDescriptor = $builder->getProjectDescriptor();

        $output->write('Collecting files .. ');
        $files = $this->getFileCollection($input);
        $output->writeln('OK');

        /** @var ProgressHelper $progress  */
        $progress = $this->getProgressBar($input);
        if (!$progress) {
            $this->connectOutputToLogging($output);
        }

        $output->write('Initializing parser .. ');
        $this->populateParser($input, $files);

        if ($progress) {
            $progress->start($output, $files->count());
        }

        try {
            $output->writeln('OK');
            $output->writeln('Parsing files');

            $mapper = new ProjectDescriptorMapper($this->getCache());
            $mapper->garbageCollect($files);
            $mapper->populate($projectDescriptor);

            $this->getParser()->parse($builder, $files, $input->getOption('sourcecode'));
        } catch (FilesNotFoundException $e) {
            throw new \Exception('No parsable files were found, did you specify any using the -f or -d parameter?');
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 0, $e);
        }

        if ($progress) {
            $progress->finish();
        }

        $output->write('Storing cache in "'.$this->getCache()->getOptions()->getCacheDir().'" .. ');
        $projectDescriptor = $builder->getProjectDescriptor();
        $mapper->save($projectDescriptor);

        $output->writeln('OK');

        return 0;
    }

    /**
     * @param InputInterface $input
     * @param Collection     $files
     */
    protected function populateParser(InputInterface $input, Collection $files)
    {
        $parser = $this->getParser();
        $this->getBuilder()->getProjectDescriptor()->setName((string)$this->getOption($input, 'title', 'title'));
        $parser->setForced($input->getOption('force'));
        $parser->setEncoding($this->getOption($input, 'encoding', 'parser/encoding'));
        $parser->setMarkers($this->getOption($input, 'markers', 'parser/markers/item', null, true));
        $parser->setIgnoredTags($input->getOption('ignore-tags'));
        $parser->setValidate($input->getOption('validate'));
        $parser->setVisibility((string)$this->getOption($input, 'visibility', 'parser/visibility'));
        $parser->setDefaultPackageName($this->getOption($input, 'defaultpackagename', 'parser/default-package-name'));
        $parser->setPath($files->getProjectRoot());
    }

    /**
     * Returns the collection of files based on the input and configuration.
     *
     * @param InputInterface $input
     *
     * @return Collection
     */
    protected function getFileCollection($input)
    {
        $files = new Collection();
        $files->setAllowedExtensions(
            $this->getOption($input, 'extensions', 'parser/extensions/extension', array('php', 'php3', 'phtml'), true)
        );
        $files->setIgnorePatterns($this->getOption($input, 'ignore', 'files/ignore', array(), true));
        $files->setIgnoreHidden($this->getOption($input, 'hidden', 'files/ignore-hidden', 'off') == 'on');
        $files->setFollowSymlinks($this->getOption($input, 'ignore-symlinks', 'files/ignore-symlinks', 'off') == 'on');

        $file_options = $this->getOption($input, 'filename', 'files/file', array(), true);
        $added_files = array();
        foreach ($file_options as $glob) {
            if (!is_string($glob)) {
                continue;
            }

            $matches = glob($glob);
            if (is_array($matches)) {
                foreach ($matches as $file) {
                    if (!empty($file)) {
                        $file = realpath($file);
                        if (!empty($file)) {
                            $added_files[] = $file;
                        }
                    }
                }
            }
        }
        $files->addFiles($added_files);

        $directory_options = $this->getOption($input, 'directory', 'files/directory', array(), true);
        $added_directories = array();
        foreach ($directory_options as $glob) {
            if (!is_string($glob)) {
                continue;
            }

            $matches = glob($glob, GLOB_ONLYDIR);
            if (is_array($matches)) {
                foreach ($matches as $dir) {
                    if (!empty($dir)) {
                        $dir = realpath($dir);
                        if (!empty($dir)) {
                            $added_directories[] = $dir;
                        }
                    }
                }
            }
        }
        $files->addDirectories($added_directories);

        return $files;
    }

    /**
     * Adds the parser.file.pre event to the advance the progressbar.
     *
     * @param InputInterface $input
     *
     * @return \Symfony\Component\Console\Helper\HelperInterface|null
     */
    protected function getProgressBar(InputInterface $input)
    {
        $progress = parent::getProgressBar($input);
        if (!$progress) {
            return null;
        }

        $this->getService('event_dispatcher')->addListener(
            'parser.file.pre',
            function (PreFileEvent $event) use ($progress) {
                $progress->advance();
            }
        );

        return $progress;
    }
}
