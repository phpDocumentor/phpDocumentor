<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Parser\Command\Project;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Cache\Storage\StorageInterface;
use Zend\I18n\Translator\Translator;
use phpDocumentor\Command\ConfigurableCommand;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\ProjectDescriptor;
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
    /** @var ProjectDescriptorBuilder $builder*/
    protected $builder;

    /** @var Parser $parser */
    protected $parser;

    /** @var Translator */
    protected $translator;

    public function __construct($builder, $parser, $translator)
    {
        $this->builder    = $builder;
        $this->parser     = $parser;
        $this->translator = $translator;

        parent::__construct('project:parse');
    }

    /**
     * @return ProjectDescriptorBuilder
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
        // minimization of the following expression
        $VALUE_OPTIONAL_ARRAY = InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY;

        $this->setAliases(array('parse'))
            ->setDescription($this->__('PPCPP-DESCRIPTION'))
            ->setHelp($this->__('PPCPP-HELPTEXT'))
            ->addOption('filename', 'f', $VALUE_OPTIONAL_ARRAY, $this->__('PPCPP:OPT-FILENAME'))
            ->addOption('directory', 'd', $VALUE_OPTIONAL_ARRAY, $this->__('PPCPP:OPT-DIRECTORY'))
            ->addOption('target', 't', InputOption::VALUE_OPTIONAL, $this->__('PPCPP:OPT-TARGET'))
            ->addOption('encoding', null, InputOption::VALUE_OPTIONAL, $this->__('PPCPP:OPT-ENCODING'))
            ->addOption('extensions', 'e', $VALUE_OPTIONAL_ARRAY, $this->__('PPCPP:OPT-EXTENSIONS'))
            ->addOption('ignore', 'i', $VALUE_OPTIONAL_ARRAY, $this->__('PPCPP:OPT-IGNORE'))
            ->addOption('ignore-tags', null, $VALUE_OPTIONAL_ARRAY, $this->__('PPCPP:OPT-IGNORETAGS'))
            ->addOption('hidden', null, InputOption::VALUE_NONE, $this->__('PPCPP:OPT-HIDDEN'))
            ->addOption('ignore-symlinks', null, InputOption::VALUE_NONE, $this->__('PPCPP:OPT-IGNORESYMLINKS'))
            ->addOption('markers', 'm', $VALUE_OPTIONAL_ARRAY, $this->__('PPCPP:OPT-MARKERS'), array('TODO', 'FIXME'))
            ->addOption('title', null, InputOption::VALUE_OPTIONAL, $this->__('PPCPP:OPT-TITLE'))
            ->addOption('force', null, InputOption::VALUE_NONE, $this->__('PPCPP:OPT-FORCE'))
            ->addOption('validate', null, InputOption::VALUE_NONE, $this->__('PPCPP:OPT-VALIDATE'))
            ->addOption('visibility', null, InputOption::VALUE_OPTIONAL, $this->__('PPCPP:OPT-VISIBILITY'))
            ->addOption('sourcecode', null, InputOption::VALUE_NONE, $this->__('PPCPP:OPT-SOURCECODE'))
            ->addOption('progressbar', 'p', InputOption::VALUE_NONE, $this->__('PPCPP:OPT-PROGRESSBAR'))
            ->addOption('parseprivate', null, InputOption::VALUE_NONE, 'PPCPP:OPT-PARSEPRIVATE')
            ->addOption(
                'defaultpackagename',
                null,
                InputOption::VALUE_OPTIONAL,
                $this->__('PPCPP:OPT-DEFAULTPACKAGENAME'),
                'Default'
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

        $target = $this->getOption($input, 'target', 'parser/target');
        if (!$this->isAbsolute($target)) {
            $target = getcwd().DIRECTORY_SEPARATOR.$target;
        }
        if (!file_exists($target)) {
            mkdir($target, 0777, true);
        }
        if (!is_dir($target)) {
            throw new \Exception($this->__('PPCPP:EXC-BADTARGET'));
        }
        $this->getCache()->getOptions()->setCacheDir($target);

        $builder = $this->getBuilder();
        $builder->createProjectDescriptor();
        $projectDescriptor = $builder->getProjectDescriptor();
        $visibility = ProjectDescriptor\Settings::VISIBILITY_DEFAULT;
        if ($input->getOption('parseprivate')) {
            $visibility = $visibility | ProjectDescriptor\Settings::VISIBILITY_INTERNAL;
        }
        $projectDescriptor->getSettings()->setVisibility($visibility);

        $output->write($this->__('PPCPP:LOG-COLLECTING'));
        $files = $this->getFileCollection($input);
        $output->writeln($this->__('PPCPP:LOG-OK'));

        /** @var ProgressHelper $progress  */
        $progress = $this->getProgressBar($input);
        if (!$progress) {
            $this->connectOutputToLogging($output);
        }

        $output->write($this->__('PPCPP:LOG-INITIALIZING'));
        $this->populateParser($input, $files);

        if ($progress) {
            $progress->start($output, $files->count());
        }

        try {
            $output->writeln($this->__('PPCPP:LOG-OK'));
            $output->writeln($this->__('PPCPP:LOG-PARSING'));

            $mapper = new ProjectDescriptorMapper($this->getCache());
            $mapper->garbageCollect($files);
            $mapper->populate($projectDescriptor);

            $this->getParser()->parse($builder, $files);
        } catch (FilesNotFoundException $e) {
            throw new \Exception($this->__('PPCPP:EXC-NOFILES'));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 0, $e);
        }

        if ($progress) {
            $progress->finish();
        }

        $output->write($this->__('PPCPP:LOG-STORECACHE', (array) $this->getCache()->getOptions()->getCacheDir()));
        $mapper->save($projectDescriptor);

        $output->writeln($this->__('PPCPP:LOG-OK'));

        return 0;
    }

    /**
     * @param InputInterface $input
     * @param Collection     $files
     */
    protected function populateParser(InputInterface $input, Collection $files)
    {
        $parser = $this->getParser();
        $title = (string) $this->getOption($input, 'title', 'title');
        $this->getBuilder()->getProjectDescriptor()->setName($title ?: 'API Documentation');
        $parser->setForced($input->getOption('force'));
        $parser->setEncoding($this->getOption($input, 'encoding', 'parser/encoding'));
        $parser->setMarkers($this->getOption($input, 'markers', 'parser/markers/item', null, true));
        $parser->setIgnoredTags($input->getOption('ignore-tags'));
        $parser->setValidate($input->getOption('validate'));
        $parser->setVisibility((string) $this->getOption($input, 'visibility', 'parser/visibility'));
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

    /**
     * Translates the provided text and replaces any contained parameters using printf notation.
     *
     * @param string   $text
     * @param string[] $parameters
     *
     * @return string
     */
    // @codingStandardsIgnoreStart
    protected function __($text, $parameters = array())
    // @codingStandardsIgnoreEnd
    {
        return vsprintf($this->translator->translate($text), $parameters);
    }
}
