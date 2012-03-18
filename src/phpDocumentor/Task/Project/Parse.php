<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Tasks
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

/**
 * Parses the given source code and creates a structure file.
 *
 * The parse task uses the source files defined either by -f or -d options and
 * generates a structure file (structure.xml) at the target location (which is
 * the folder 'output' unless the option -t is provided).
 *
 * @category   phpDocumentor
 * @package    Tasks
 * @subpackage Project
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 *
 * @method boolean getSourcecode() flag indicating whether the source code needs
 *  to be parsed as well.
 */
class phpDocumentor_Task_Project_Parse extends phpDocumentor_Task_Abstract
{
    /** @var string The name of this task including namespace */
    protected $taskname = 'project:parse';

    /**
     * Sets the options and description for this task.
     *
     * @return void
     */
    protected function configure()
    {
        $this->addOption(
            'f|filename', '=s',
            'Comma-separated list of files to parse. The wildcards ? and * are '
            .'supported'
        );
        $this->addOption(
            'd|directory', '=s',
            'Comma-separated list of directories to (recursively) parse.'
        );
        $this->addOption(
            't|target', '-s',
            'Path where to store the generated output (optional, defaults to "'
            . phpDocumentor_Core_Abstract::config()->target . '")'
        );
        $this->addOption(
            'e|extensions', '-s',
            'Optional comma-separated list of extensions to parse, defaults to '
            . 'php, php3 and phtml'
        );
        $this->addOption(
            'i|ignore', '-s',
            'Comma-separated list of file(s) and directories that will be '
            . 'ignored. Wildcards * and ? are supported'
        );
        $this->addOption(
            'ignore-tags', '-s',
            'Comma-separated list of tags that will be ignored, defaults to none. '
            . '@package, @subpackage and @ignore may not be ignored.'
        );
        $this->addOption(
            'hidden', '-s',
            'set to on to descend into hidden directories '
            .'(directories starting with \'.\'), default is on'
        );
        $this->addOption(
            'ignore-symlinks', '-s',
            'Ignore symlinks to other files or directories, default is on'
        );
        $this->addOption(
            'm|markers', '-s',
            'Comma-separated list of markers/tags to filter, (optional, '
            . 'defaults to: "TODO,FIXME")'
        );
        $this->addOption(
            'v|verbose', '',
            'Provides additional information during parsing, usually only '
            . 'needed for debugging purposes'
        );
        $this->addOption(
            'title', '-s',
            'Sets the title for this project; default is the phpDocumentor logo'
        );
        $this->addOption(
            'force', '',
            'Forces a full build of the documentation, does not increment '
            . 'existing documentation'
        );
        $this->addOption(
            'validate', '',
            'Validates every processed file using PHP Lint, costs a lot of '
            . 'performance'
        );
        $this->addOption(
            'visibility', '-s',
            'Specifies the parse visibility that should be displayed in the '
            . 'documentation (comma seperated e.g. "public,protected")'
        );
        $this->addOption(
            'defaultpackagename', '-s',
            'name to use for the default package.  If not specified, uses '
            . '"default"'
        );
        $this->addOption(
            'sourcecode', '',
            'Whether to include syntax highlighted source code'
        );
        $this->addOption(
            'p|progressbar', '',
            'Whether to show a progress bar; will automatically quiet logging '
            . 'to stdout'
        );
    }

    /**
    * Returns the target location where to store the structure.xml.
    *
    * @throws Zend_Console_Getopt_Exception
    *
    * @return string
    */
    public function getTarget()
    {
        $target = parent::getTarget();
        $target = ($target === null)
          ? trim(phpDocumentor_Core_Abstract::config()->parser->target)
          : trim($target);

        if (($target == '') || ($target == DIRECTORY_SEPARATOR)) {
            throw new Zend_Console_Getopt_Exception(
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
                throw new Zend_Console_Getopt_Exception(
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
            throw new Zend_Console_Getopt_Exception(
                'The given path "' . $target . '" either does not exist or is '
                . 'not writable.'
            );
        }

        return $target;
    }

    /**
     * Retrieves a list of allowed extensions.
     *
     * @return string[]
     */
    public function getExtensions()
    {
        if (parent::getExtensions() !== null) {
            return explode(',', parent::getExtensions());
        }

        return phpDocumentor_Core_Abstract::config()->getArrayFromPath(
            'parser/extensions/extension'
        );
    }

    /**
     * Returns all ignore patterns.
     *
     * @return array
     */
    public function getIgnore()
    {
        if (parent::getIgnore() !== null) {
            return explode(',', parent::getIgnore());
        }

        return phpDocumentor_Core_Abstract::config()->getArrayFromPath('files/ignore');
    }

    /**
     * Returns whether hidden files should be ignored.
     *
     * @return bool
     */
    public function getHidden()
    {
        if (parent::getHidden() !== null) {
            return (bool)(parent::getHidden() !== 'off');
        }

        return (bool)(phpDocumentor_Core_Abstract::config()->files->{'ignore-hidden'}
            !== 'off');
    }

    /**
     * Returns the title for this project.
     *
     * @return string
     */
    public function getTitle()
    {
        if (parent::getTitle() !== null) {
            return parent::getTitle();
        }

        return phpDocumentor_Core_Abstract::config()->get('title');
    }

    /**
     * Returns the name of the default package.
     *
     * @return string
     */
    public function getDefaultpackagename()
    {
        if (parent::getDefaultpackagename() !== null) {
            return parent::getDefaultpackagename();
        }

        return phpDocumentor_Core_Abstract::config()->parser->get('default-package-name');
    }

    /**
     * Configuration override for setting the parser visibility
     *
     * By default it will use the command line options first, and then
     * look at the config file if no options have been supplied
     *
     * @return string
     */
    public function getVisibility()
    {
        if (parent::getVisibility() !== null) {
            return parent::getVisibility();
        }

        return phpDocumentor_Core_Abstract::config()->parser->visibility;
    }

    /**
     * Returns the list of markers to scan for and summize in their separate page.
     *
     * @return string[]
     */
    public function getMarkers()
    {
        if (parent::getMarkers()) {
            return explode(',', parent::getMarkers());
        }

        return phpDocumentor_Core_Abstract::config()->getArrayFromPath(
            'parser/markers/item'
        );
    }

    /**
     * Returns the list of tags to ignore.
     *
     * @return string[]
     */
    public function getIgnoreTags()
    {
        $method = 'getIgnore-tags';
        if (parent::$method()) {
            return explode(',', parent::$method());
        }

        return phpDocumentor_Core_Abstract::config()->getArrayFromPath(
            'parser/tags/ignore'
        );
    }

    /**
     * Returns whether to ignore symlinks or not.
     *
     * @return bool
     */
    public function getIgnoreSymlinks()
    {
        $method = 'getIgnore-symlinks';
        if (parent::$method()) {
            return (bool)(parent::$method() !== 'off');
        }

        return (bool)(phpDocumentor_Core_Abstract::config()->files->{'ignore-symlinks'}
            !== 'off');
    }

    /**
     * Outputs a progress indication.
     *
     * The event argument contains a property 'progress' which is an array with
     * 2 elements,
     *
     * 1. the current progress indicator and
     * 2. the maximum progress value
     *
     * @param sfEvent $event event containing the progress current value and
     * maximum value.
     *
     * @return void.
     */
    public function echoProgress(sfEvent $event)
    {
        echo '.';
        if (($event['progress'][0] % 70 == 0)
            || ($event['progress'][0] % $event['progress'][1] == 0)
        ) {
            echo ' ' . $event['progress'][0] . '/' . $event['progress'][1] . PHP_EOL;
        }
    }

    /**
     * Execute the parsing process.
     *
     * @throws Zend_Console_Getopt_Exception
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getProgressbar()) {
            phpDocumentor_Parser_Abstract::$event_dispatcher->connect(
                'parser.file.pre', array($this, 'echoProgress')
            );
            $this->setQuiet(true);
        }

        $files = new phpDocumentor_Parser_Files();
        $files->setAllowedExtensions($this->getExtensions());
        $files->setIgnorePatterns($this->getIgnore());
        $files->setIgnoreHidden(!$this->getHidden());
        $files->setFollowSymlinks(!$this->getIgnoreSymlinks());

        $paths = array_unique(
            $this->getFilename()
            ? explode(',', $this->getFilename())
            : phpDocumentor_Core_Abstract::config()->getArrayFromPath('files/file')
        );
        $files->addFiles($paths);

        $paths = array_unique(
            $this->getDirectory()
            ? explode(',', $this->getDirectory())
            : phpDocumentor_Core_Abstract::config()->getArrayFromPath('files/directory')
        );

        $files->addDirectories($paths);

        $parser = new phpDocumentor_Parser();
        $parser->setTitle(htmlentities($this->getTitle()));
        $parser->setExistingXml($this->getTarget());
        $parser->setForced($this->getForce());
        $parser->setMarkers($this->getMarkers());
        $parser->setIgnoredTags($this->getIgnoreTags());
        $parser->setValidate($this->getValidate());
        $parser->setVisibility($this->getVisibility());
        $parser->setDefaultPackageName($this->getDefaultpackagename());

        $parser->setPath($files->getProjectRoot());

        try {
            // save the generate file to the path given as the 'target' option
            file_put_contents(
                $this->getTarget(),
                $parser->parseFiles($files, $this->getSourcecode())
            );
        } catch (Exception $e) {
            if ($e->getCode() === phpDocumentor_Parser_Exception::NO_FILES_FOUND) {
                throw new Zend_Console_Getopt_Exception(
                    'No parsable files were found, did you specify any using '
                    . 'the -f or -d parameter?'
                );
            }

            throw new Zend_Console_Getopt_Exception($e->getMessage());
        }
    }
}
