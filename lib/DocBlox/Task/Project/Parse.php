<?php
/**
 * Parses the given source code and populate a structure file.
 *
 * @package    DocBlox
 * @subpackage Tasks
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Task_Project_Parse extends DocBlox_Task_Abstract
{
  protected $taskname = 'project:parse';

  /**
   * Sets the options and description for this task.
   *
   * @return void
   */
  protected function configure()
  {
    $this->setUsageDescription(<<<DESC
The parse method uses the source files that you have using either the -f or -d method and generates a structure
file (structure.xml) in the target location (which is the folder 'output' unless the option -t is provided).
DESC
);
    $this->addOption('f|filename', '=s',
      'Comma-separated list of files to parse. The wildcards ? and * are supported'
    );
    $this->addOption('d|directory', '=s',
      'Comma-separated list of directories to (recursively) parse.'
    );
    $this->addOption('t|target', '-s',
      'Path where to store the generated output (optional, defaults to "output")'
    );
    $this->addOption('e|extensions', '-s',
      'Optional comma-separated list of extensions to parse, defaults to php, php3 and phtml'
    );
    $this->addOption('i|ignore', '-s',
      'Comma-separated list of file(s) and directories that will be ignored. Wildcards * and ? are supported'
    );
    $this->addOption('m|markers', '-s',
      'Comma-separated list of markers/tags to filter, (optional, defaults to: "TODO,FIXME")'
    );
    $this->addOption('c|config', '-s',
      'Configuration filename, if none is given the defaults of the docblox.config.xml in the root of DocBlox is used'
    );
    $this->addOption('v|verbose', '',
      'Provides additional information during parsing, usually only needed for debugging purposes'
    );
    $this->addOption('q|quiet', '',
      'Silences the output and logging'
    );
    $this->addOption('force', '',
      'Forces a full build of the documentation, does not increment existing documentation'
    );
    $this->addOption('validate', '',
      'Validates every processed file using PHP Lint, costs a lot of performance'
    );
  }

  /**
   * Overwrite header output to not show anything when 'Quiet' mode is on.
   *
   * @return void
   */
  protected function outputHeader()
  {
    if ($this->getQuiet())
    {
      return;
    }

    parent::outputHeader();
  }

  /**
   * Additionally checks whether the given filename is readable.
   *
   * @throws InvalidArgumentException
   *
   * @param string $value
   *
   * @return void
   */
  public function setConfig($value)
  {
    if (!is_readable($value))
    {
      throw new InvalidArgumentException('Config file "' . $value . '" is not readable');
    }

    parent::setConfig($value);
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
      ? trim(DocBlox_Abstract::config()->target)
      : trim($target);

    if (($target == '') || ($target == DIRECTORY_SEPARATOR))
    {
      throw new Zend_Console_Getopt_Exception('Either an empty path or root was given');
    }

    if (!is_dir($target))
    {
      throw new Zend_Console_Getopt_Exception('The given location "' . $target . '" is not a folder');
    }

    if (!is_writable($target))
    {
      throw new Zend_Console_Getopt_Exception('The given path "' . $target . '" either does not exist or is not writable.');
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
    if (parent::getExtensions() !== null)
    {
      return explode(',', parent::getExtensions());
    }

    return DocBlox_Abstract::config()->getArrayFromPath('extensions/extension');
  }

  /**
   * Returns all ignore patterns.
   *
   * @return array
   */
  public function getIgnore()
  {
    if (parent::getIgnore() !== null)
    {
      return explode(',', parent::getIgnore());
    }

    return DocBlox_Abstract::config()->getArrayFromPath('ignore/item');
  }

  /**
   * Interprets the -d and -f options and retrieves all filenames.
   *
   * This method does take the extension option into account but _not_ the
   * ignore list. The ignore list is handled in the parser.
   *
   * @todo method contains duplicate code and is too large, refactor
   * @todo consider moving the filtering on ignore_paths here
   *
   * @return string[]
   */
  protected function parseFiles()
  {
    // read the filename argument in search for files (wildcards are explicitly allowed)
    $expressions = array_unique(
      $this->getFilename()
        ? explode(',', $this->getFilename())
        : DocBlox_Abstract::config()->getArrayFromPath('files/file')
    );

    $files = array();
    foreach ($expressions as $expr)
    {
      // search file(s) with the given expressions
      $result = glob($expr);
      foreach ($result as $file)
      {
        // if the path is not a file OR it's extension does not match the given, then do not process it.
        if (!is_file($file) || !in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $this->getExtensions()))
        {
          continue;
        }

        $files[] = realpath($file);
      }
    }

    $expressions = array_unique(
      $this->getDirectory()
        ? explode(',', $this->getDirectory())
        : DocBlox_Abstract::config()->getArrayFromPath('files/directory')
    );

    foreach ($expressions as $directory)
    {
      // if the given is not a directory, skip it
      if (!is_dir($directory))
      {
        continue;
      }

      // get all files recursively to the files array
      $files_iterator = new RecursiveDirectoryIterator($directory);

      /** @var SplFileInfo $file */
      foreach (new RecursiveIteratorIterator($files_iterator) as $file)
      {
        // skipping dots (should any be encountered)
        if (($file->getFilename() == '.') || ($file->getFilename() == '..'))
        {
          continue;
        }

        // check if the file has the correct extension
        if (!in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $this->getExtensions()))
        {
          continue;
        }

        $files[] = $file->getRealPath();
      }
    }

    return array_unique($files);
  }

  /**
   * Returns the list of markers to scan for and summize in their separate page.
   *
   * @return string[]
   */
  public function getMarkers()
  {
    if (parent::getMarkers())
    {
      return explode(',', parent::getMarkers());
    }

    return DocBlox_Abstract::config()->getArrayFromPath('markers/item');
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
    if ($this->getConfig())
    {
      DocBlox_Abstract::config()->merge(new Zend_Config_Xml($this->getConfig()));
    }

    $files = $this->parseFiles();
    if (count($files) < 1)
    {
      throw new Zend_Console_Getopt_Exception('No parsable files were found, did you specify any using the -f or -d parameter?');
    }

    $parser = new DocBlox_Parser();
    if ($this->getVerbose())
    {
      $parser->setLogLevel(DocBlox_Log::DEBUG);
    }
    if ($this->getQuiet())
    {
      $parser->setLogLevel(DocBlox_Log::QUIET);
    }
    $parser->setExistingXml($this->getTarget() . '/structure.xml');
    $parser->setIgnorePatterns($this->getIgnore());
    $parser->setForced($this->getForce());
    $parser->setMarkers($this->getMarkers());
    $parser->setValidate($this->getValidate());
    $parser->setPath($this->getDirectory());

    // save the generate file to the path given as the 'target' option
    file_put_contents($this->getTarget() . '/structure.xml', $parser->parseFiles($files));
  }
}