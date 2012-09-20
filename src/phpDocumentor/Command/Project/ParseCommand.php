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
namespace phpDocumentor\Command\Project;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use phpDocumentor\Parser\Event\PreFileEvent;

/**
 * Parses the given source code and creates a structure file.
 *
 * The parse task uses the source files defined either by -f or -d options and
 * generates a structure file (structure.xml) at the target location (which is
 * the folder 'output' unless the option -t is provided).
 */
class ParseCommand extends \phpDocumentor\Command\ConfigurableCommand
{
    /**
     * Initializes this command and sets the name, description, options and
     * arguments.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('project:parse')
            ->setAliases(array('parse'))
            ->setDescription('Creates a structure file from your source code')
            ->setHelp(
<<<HELP
The parse task uses the source files defined either by -f or -d options and
generates a structure file (structure.xml) at the target location.
HELP
            )
            ->addOption(
                'target', 't',
                InputOption::VALUE_OPTIONAL,
                'Path where to store the generated output'
            )
            ->addOption(
                'filename', 'f',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of files to parse. The wildcards ? and * '
                .'are supported'
            )
            ->addOption(
                'directory', 'd',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of directories to (recursively) parse'
            )
            ->addOption(
                'extensions', 'e',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of extensions to parse, defaults to '
                . 'php, php3 and phtml'
            )
            ->addOption(
                'ignore', 'i',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of file(s) and directories that will be '
                . 'ignored. Wildcards * and ? are supported'
            )
            ->addOption(
                'ignore-tags', null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of tags that will be ignored, defaults to '
                .'none. package, subpackage and ignore may not be ignored.'
            )
            ->addOption(
                'hidden', null,
                InputOption::VALUE_NONE,
                'set to on to descend into hidden directories '
                . '(directories starting with \'.\'), default is on'
            )
            ->addOption(
                'ignore-symlinks', null,
                InputOption::VALUE_NONE,
                'Ignore symlinks to other files or directories, default is on'
            )
            ->addOption(
                'markers', 'm',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Comma-separated list of markers/tags to filter',
                array('TODO', 'FIXME')
            )
            ->addOption(
                'title', null,
                InputOption::VALUE_OPTIONAL,
                'Sets the title for this project; default is the phpDocumentor '
                .'logo'
            )
            ->addOption(
                'force', null,
                InputOption::VALUE_NONE,
                'Forces a full build of the documentation, does not increment '
                .'existing documentation'
            )
            ->addOption(
                'validate', null,
                InputOption::VALUE_NONE,
                'Validates every processed file using PHP Lint, costs a lot of '
                .'performance'
            )
            ->addOption(
                'visibility', null,
                InputOption::VALUE_OPTIONAL,
                'Specifies the parse visibility that should be displayed in the '
                .'documentation (comma seperated e.g. "public,protected")'
            )
            ->addOption(
                'defaultpackagename', null,
                InputOption::VALUE_OPTIONAL,
                'Name to use for the default package.',
                'Default'
            )
            ->addOption(
                'sourcecode', null,
                InputOption::VALUE_NONE,
                'Whether to include syntax highlighted source code'
            )
            ->addOption(
                'progressbar', 'p',
                InputOption::VALUE_NONE,
                'Whether to show a progress bar; will automatically quiet logging '
                .'to stdout'
            );

        parent::configure();
    }

    /**
     * Returns the target location where to store the structure.xml.
     *
     * @param string $target
     *
     * @throws \InvalidArgumentException if an empty path or root was provided
     * @throws \InvalidArgumentException if the target location could not be
     *     created
     * @throws \InvalidArgumentException if the target location is not a folder
     * @throws \InvalidArgumentException if the target location is not writable
     *
     * @return string
     */
    public function getTarget($target)
    {
        $target = trim($target);
        if (($target == '') || ($target == DIRECTORY_SEPARATOR)) {
            throw new \InvalidArgumentException(
                'Either an empty path or root was given: ' . $target
            );
        }

        // convert target to absolute path to satisfy phar packaging
        if (!$this->isAbsolute($target)) {
            $target = getcwd().DIRECTORY_SEPARATOR.$target;
        }

        // if the target does not end with .xml, assume it is a folder
        if (substr($target, -4) != '.xml') {
            // if the folder does not exist at all, create it
            if (!file_exists($target)) {
                if (!@mkdir($target, 0755, true)) {
                    throw new \InvalidArgumentException(
                        'The path "' . $target . '" could not be created'
                    );
                }
            }

            if (!is_dir($target)) {
                throw new \InvalidArgumentException(
                    'The given location "' . $target . '" is not a folder'
                );
            }

            $path = realpath($target);
            $target = $path . DIRECTORY_SEPARATOR . 'structure.xml';
        } else {
            $path = realpath(dirname($target));
            $target = $path . DIRECTORY_SEPARATOR . basename($target);
        }

        if (!is_writable($path)) {
            throw new \InvalidArgumentException(
                'The given path "' . $target . '" either does not exist or is '
                . 'not writable.'
            );
        }

        return $target;
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // invoke parent to load custom config
        parent::execute($input, $output);

        /** @var \phpDocumentor\Console\Helper\ProgressHelper $progress  */
        $progress = $this->getProgressBar($input);
        if (!$progress) {
            $this->connectOutputToLogging($output);
        }

        $output->write('Initializing parser and collecting files .. ');
        $target = $this->getTarget(
            $this->getOption($input, 'target', 'parser/target')
        );

        $parser = new \phpDocumentor\Parser\Parser();
        $parser->setTitle(
            (string)$this->getOption($input, 'title', 'title')
        );
        $parser->setExistingXml($target);
        $parser->setForced($input->getOption('force'));
        $parser->setMarkers(
            $this->getOption($input, 'markers', 'parser/markers/item', null, true)
        );
        $parser->setIgnoredTags($input->getOption('ignore-tags'));
        $parser->setValidate($input->getOption('validate'));
        $parser->setVisibility(
            (string)$this->getOption($input, 'visibility', 'parser/visibility')
        );
        $parser->setDefaultPackageName(
            $this->getOption(
                $input, 'defaultpackagename', 'parser/default-package-name'
            )
        );

        $files = $this->getFileCollection($input);
        $parser->setPath($files->getProjectRoot());

        if ($progress) {
            $progress->start($output, $files->count());
        }

        try {
            // save the generate file to the path given as the 'target' option
            $output->writeln('OK');
            $output->writeln('Parsing files');
            $result = $parser->parseFiles($files, $input->getOption('sourcecode'));
        } catch (\Exception $e) {
            if ($e->getCode() === \phpDocumentor\Parser\Exception::NO_FILES_FOUND) {
                throw new \Exception(
                    'No parsable files were found, did you specify any using '
                    . 'the -f or -d parameter?'
                );
            }

            throw new \Exception($e->getMessage());
        }

        if ($progress) {
            $progress->finish();
        }

        $output->write('Storing structure.xml in "'.$target.'" .. ');
        file_put_contents($target, $result);
        $output->writeln('OK');

        return 0;
    }

    /**
     * Returns the collection of files based on the input and configuration.
     *
     * @param InputInterface $input
     *
     * @return \phpDocumentor\File\Collection
     */
    protected function getFileCollection($input)
    {
        $files = new \phpDocumentor\Fileset\Collection();
        $files->setAllowedExtensions(
            $this->getOption(
                $input, 'extensions', 'parser/extensions/extension',
                array('php', 'php3', 'phtml'), true
            )
        );
        $files->setIgnorePatterns(
            $this->getOption($input, 'ignore', 'files/ignore', array(), true)
        );
        $files->setIgnoreHidden(
            $this->getOption(
                $input, 'hidden', 'files/ignore-hidden', 'off'
            ) == 'on'
        );
        $files->setFollowSymlinks(
            $this->getOption(
                $input, 'ignore-symlinks', 'files/ignore-symlinks', 'off'
            ) == 'on'
        );

        $file_options = $this->getOption(
            $input, 'filename', 'files/file', array(), true
        );
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

        $directory_options = $this->getOption(
            $input, 'directory', 'files/directory', array(), true
        );
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
