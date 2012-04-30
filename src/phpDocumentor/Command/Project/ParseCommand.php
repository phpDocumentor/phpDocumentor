<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Command\Project;

use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * Parses the given source code and creates a structure file.
 *
 * The parse task uses the source files defined either by -f or -d options and
 * generates a structure file (structure.xml) at the target location (which is
 * the folder 'output' unless the option -t is provided).
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class ParseCommand extends \phpDocumentor\Command\Command
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
            ->setDescription('')
            ->setHelp(
<<<HELP
The parse task uses the source files defined either by -f or -d options and
generates a structure file (structure.xml) at the target location.
HELP
            )
            ->addOption(
                'target', 't',
                InputOption::VALUE_REQUIRED,
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
                'Path where to store the generated output'
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
    }

    /**
    * Returns the target location where to store the structure.xml.
    *
    * @throws \InvalidArgumentException
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

        // if the target does not end with .xml, assume it is a folder
        if (substr($target, -4) != '.xml') {
            // if the folder does not exist at all, create it
            if (!file_exists($target)) {
                mkdir($target, 0744, true);
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
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('progressbar')) {
            \phpDocumentor_Parser_Abstract::$event_dispatcher->connect(
                'parser.file.pre', array($this, 'echoProgress')
            );
        }

        $target = $this->getTarget(
            $this->getOption($input, 'target', 'parser/target')
        );

        $files = new \phpDocumentor_Parser_Files();
        $files->setAllowedExtensions(
            (array)$this->getOption(
                $input, 'extensions', 'parser/extensions/extension',
                array('php', 'php3', 'phtml')
            )
        );
        $files->setIgnorePatterns(
            (array)$this->getOption($input, 'ignore', 'files/ignore', array())
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
        $files->addFiles(
            (array)$this->getOption($input, 'filename', 'files/file', array())
        );

        $files->addDirectories(
            (array)$this->getOption($input, 'directory', 'files/directory', array())
        );

        $parser = new \phpDocumentor_Parser();
        $parser->setTitle(htmlentities((string)$this->getOption($input, 'title', 'title')));
        $parser->setExistingXml($target);
        $parser->setForced($input->getOption('force'));
        $parser->setMarkers(
            (array)$this->getOption($input, 'markers', 'parser/markers/item')
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

        $parser->setPath($files->getProjectRoot());

        try {
            // save the generate file to the path given as the 'target' option
            file_put_contents(
                $target,
                $parser->parseFiles($files, $input->getOption('sourcecode'))
            );
        } catch (\Exception $e) {
            if ($e->getCode() === \phpDocumentor_Parser_Exception::NO_FILES_FOUND) {
                throw new \Exception(
                    'No parsable files were found, did you specify any using '
                    . 'the -f or -d parameter?'
                );
            }

            throw new \Exception($e->getMessage());
        }
    }
}
