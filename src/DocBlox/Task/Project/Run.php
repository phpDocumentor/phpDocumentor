<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Tasks
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Parse and transform the given directory (-d|-f) to the given location (-t).
 *
 * DocBlox creates documentation from PHP source files. The simplest way to use it is:
 *
 *     $ docblox run -d <directory to parse> -t <output directory>
 *
 * This will parse every file ending with .php, .php3 and .phtml in <directory to parse> and then
 * output a HTML site containing easily readable documentation in <output directory>.
 *
 * DocBlox will try to look for a docblox.dist.xml or docblox.xml file in your current working directory
 * and use that to override the default settings if present. In the configuration file can you specify the
 * same settings (and more) as the command line provides.
 *
 * @category    DocBlox
 * @package     Tasks
 * @subpackage  Project
 * @author      Mike van Riel <mike.vanriel@naenius.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 * @link        http://docblox-project.org
 */
class DocBlox_Task_Project_Run extends DocBlox_Task_Abstract
{
    /** @var string The name of this task including namespace */
    protected $taskname = 'project:run';

    /**
     * Sets the options and description for this task.
     *
     * @return void
     */
    protected function configure()
    {
        $this->addOption(
            'f|filename', '=s',
            'Comma-separated list of files to parse. The wildcards ? and * are supported'
        );
        $this->addOption(
            'd|directory', '=s',
            'Comma-separated list of directories to (recursively) parse.'
        );
        $this->addOption(
            't|target', '-s',
            'Path where to store the generated output (optional, defaults to "output")'
        );
        $this->addOption(
            'e|extensions', '-s',
            'Optional comma-separated list of extensions to parse, defaults to php, php3 and phtml'
        );
        $this->addOption(
            'i|ignore', '-s',
            'Comma-separated list of file(s) and directories that will be ignored. Wildcards * and ? are supported'
        );
        $this->addOption(
            'm|markers', '-s',
            'Comma-separated list of markers/tags to filter, (optional, defaults to: "TODO,FIXME")'
        );
        $this->addOption(
            'v|verbose', '',
            'Provides additional information during parsing, usually only needed for debugging purposes'
        );
        $this->addOption(
            'title', '-s',
            'Sets the title for this project; default is the DocBlox logo'
        );
        $this->addOption(
            'template', '-s',
            'Sets the template to use when generating the output'
        );
        $this->addOption(
            'force', '',
            'Forces a full build of the documentation, does not increment existing documentation'
        );
        $this->addOption(
            'validate', '',
            'Validates every processed file using PHP Lint, costs a lot of performance'
        );
        $this->addOption(
            'parseprivate', '',
            'Whether to parse DocBlocks tagged with @internal'
        );
        $this->addOption(
            'visibility', '-s',
            'Specifies the parse visibility that should be displayed in the documentation (comma seperated e.g. "public,protected")'
        );
        $this->addOption(
            'defaultpackagename', '-s',
            'name to use for the default package.  If not specified, uses "default"'
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
     * Executes the transformation process.
     *
     * @throws Zend_Console_Getopt_Exception
     *
     * @return void
     */
    public function execute()
    {
        $task = new DocBlox_Task_Project_Parse();
        $task->setFilename($this->getFilename());
        $task->setDirectory($this->getDirectory());
        if ($this->getTarget() !== null) {
            $task->setTarget($this->getTarget());
        }
        $task->setExtensions($this->getExtensions());
        $task->setIgnore($this->getIgnore());
        $task->setMarkers($this->getMarkers());
        $task->setConfig($this->getConfig());
        $task->setVerbose($this->getVerbose());
        $task->setQuiet($this->getQuiet());
        $task->setTitle($this->getTitle());
        $task->setForce($this->getForce());
        $task->setValidate($this->getValidate());
        $task->setVisibility($this->getVisibility());
        $task->setDefaultpackagename($this->getDefaultpackagename());
        $task->setProgressbar($this->getProgressbar());
        $task->setSourcecode($this->getSourcecode());
        $task->execute();

        $transform = new DocBlox_Task_Project_Transform();
        if ($this->getTarget() !== null) {
            $transform->setTarget($task->getTarget());
        }
        $transform->setTemplate($this->getTemplate());
        $transform->setSource($task->getTarget() . DIRECTORY_SEPARATOR . 'structure.xml');
        $transform->setVerbose($task->getVerbose());
        $transform->setQuiet($task->getQuiet());
        $transform->setParseprivate($this->getParseprivate());
        $transform->setProgressbar($this->getProgressbar());
        $transform->execute();
    }

}