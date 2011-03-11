<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    CLI
 * @copyright  Copyright (c) 2010-2010 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Contains the arguments for the parser.
 *
 * @category   DocBlox
 * @package    CLI
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Arguments extends DocBlox_Task_Abstract
{
  static public $parse_options = array(
      'h|help'         => 'Show this help message',
      'c|config-s'     => 'Configuration filename, if none is given the defaults of the docblox.config.xml in the root of DocBlox is used',
      'f|filename=s'   => 'Comma-separated list of files to parse. The wildcards ? and * are supported',
      'd|directory=s'  => 'Comma-separated list of directories to (recursively) parse.',
      'e|extensions-s' => 'Optional comma-separated list of extensions to parse, defaults to php, php3 and phtml',
      't|target-s'     => 'Path where to store the generated output (optional, defaults to "output")',
      'v|verbose'      => 'Provides additional information during parsing, usually only needed for debuggin purposes',
      'i|ignore-s'     => 'Comma-separated list of file(s) and directories that will be ignored. Wildcards * and ? are supported',
      'm|markers-s'    => 'Comma-separated list of markers/tags to filter, (optional, defaults to: "TODO,FIXME")',
      'force'          => 'Forces a full build of the documentation, does not increment existing documentation',
      'validate'       => 'Validates every processed file using PHP Lint, costs a lot of performance',
    );

  static public $transform_options = array(
    'h|help'     => 'show this help message',
    's|source-s' => 'path where the structure.xml is located (optional, defaults to "output/structure.xml")',
    't|target-s' => 'path where to save the generated files (optional, defaults to "output")',
    'template-s' => 'name of the theme to use (optional, defaults to "default")',
    'v|verbose'  => 'Outputs any information collected by this application, may slow down the process slightly',
  );

  protected function configure()
  {

  }

  protected function execute()
  {

  }

  /**
   * Contains all files identified using the -d and -f option
   *
   * @var string[]
   */
  protected $files = array();

  /**
   * Contains an list of file extensions which files will be parsed
   *
   * @var string[]
   */
  protected $allowed_extensions = null;

  /**
   * Initializes the object with all supported parameters.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct(self::$parse_options);
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
    $files = array();

    // get the file from the config is present
    $config_files = (isset(DocBlox_Abstract::config()->files) && isset(DocBlox_Abstract::config()->files->file)) ?
      DocBlox_Abstract::config()->files->file :
      array();
    if ($config_files instanceof Zend_Config)
    {
      $config_files = $config_files->toArray();
    }
    if (is_string($config_files))
    {
      $config_files = array($config_files);
    }

    // read the filename argument in search for files (wildcards are explicitly allowed)
    $expressions = $this->getOption('filename') ?
      explode(',', $this->getOption('filename')) :
      $config_files;
    $expressions = array_unique($expressions);

    foreach($expressions as $expr)
    {
      // search file(s) with the given expressions
      $result = glob($expr);

      foreach ($result as $key => $file)
      {
        // check if the file has the correct extension
        $info = pathinfo($file);
        if (isset($info['extension']) && in_array(strtolower($info['extension']), $this->getExtensions()) )
        {
          continue;
        }

        // check if the given is a file
        if (!is_file($file))
        {
          unset($result[$key]);
        }
      }

      // add all legal files to the files array
      $files = array_merge($files, $result);
    }

    // get the file from the config is present
    $config_dirs = (isset(DocBlox_Abstract::config()->files) && isset(DocBlox_Abstract::config()->files->directory)) ?
      DocBlox_Abstract::config()->files->directory :
      array();
    if ($config_dirs instanceof Zend_Config)
    {
      $config_dirs = $config_dirs->toArray();
    }
    if (is_string($config_dirs))
    {
      $config_dirs = array($config_dirs);
    }

    // get all directories which must be recursively checked
    $option_directories = $this->getOption('directory') ?
      explode(',', $this->getOption('directory')) :
      $config_dirs;

    foreach ($option_directories as $directory)
    {
      // if the given is not a directory, skip it
      if (!is_dir($directory))
      {
        continue;
      }

      // get all files recursively to the files array
      $files_iterator = new RecursiveDirectoryIterator($directory);

      /** @var SplFileInfo $file */
      foreach(new RecursiveIteratorIterator($files_iterator) as $file)
      {
        // skipping dots (should any be encountered)
        if (($file->getFilename() == '.') || ($file->getFilename() == '..'))
        {
          continue;
        }

        // check if the file has the correct extension
        $info = pathinfo($file);
        if (!isset($info['extension']) || !in_array(strtolower($info['extension']), $this->getExtensions()))
        {
          continue;
        }

        $files[] = $file->getPathname();
      }
    }

    return $files;
  }

  /**
   * Retrieves the list of files filtered by extension.
   *
   * @return string[]
   */
  public function getFiles()
  {
    if (!$this->files)
    {
      $this->files = $this->parseFiles();
    }

    return $this->files;
  }

  /**
   * Retrieves a list of allowed extensions.
   *
   * @return string[]
   */
  public function getExtensions()
  {
    if ($this->allowed_extensions === null)
    {
      if ($this->getOption('extensions'))
      {
        $this->allowed_extensions = explode(',', $this->getOption('extensions'));
      }
      else
      {
        $this->allowed_extensions = DocBlox_Abstract::config()->extensions->extension->toArray();
      }
    }

    return $this->allowed_extensions;
  }

  /**
   * Retrieves the path to save the result to.
   *
   * @throws Zend_Console_Getopt_Exception
   *
   * @return string
   */
  public function getTarget()
  {
    $target = $this->getOption('target');
    if ($target === null)
    {
      $target = DocBlox_Abstract::config()->target;
    }

    $target = trim($target);
    if (($target == '') || ($target == DIRECTORY_SEPARATOR))
    {
      throw new Zend_Console_Getopt_Exception('Either an empty path or root was given');
    }

    if (!is_writable($target))
    {
      throw new Zend_Console_Getopt_Exception('The given path "'.$target.'" either does not exist or is not writable.');
    }

    // remove any ending slashes
    $target = rtrim($target, DIRECTORY_SEPARATOR);

    return $target;
  }

  /**
   * Returns all ignore patterns.
   *
   * @todo consider moving the conversion from glob to regex to here.
   *
   * @return array
   */
  public function getIgnorePatterns()
  {
    // get the file from the config is present
    $config_ignores = (isset(DocBlox_Abstract::config()->ignore) && isset(DocBlox_Abstract::config()->ignore->item)) ?
      DocBlox_Abstract::config()->ignore->item :
      array();
    if ($config_ignores instanceof Zend_Config)
    {
      $config_ignores = $config_ignores->toArray();
    }
    if (is_string($config_ignores))
    {
      $config_ignores = array($config_ignores);
    }

    // get all directories which must be recursively checked
    $option_ignore = $this->getOption('ignore') ?
      explode(',', $this->getOption('ignore')) :
      $config_ignores;

    return $option_ignore;
  }

  /**
   * Returns the list of markers to scan for and summize in their separate page.
   *
   * @return string[]
   */
  public function getMarkers()
  {
    // get the file from the config is present
    $config_markers = (isset(DocBlox_Abstract::config()->markers) && isset(DocBlox_Abstract::config()->markers->item)) ?
      DocBlox_Abstract::config()->markers->item :
      array();
    if ($config_markers instanceof Zend_Config)
    {
      $config_markers = $config_markers->toArray();
    }
    if (is_string($config_markers))
    {
      $config_markers = array($config_markers);
    }

    // get all directories which must be recursively checked
    $option_marker = $this->getOption('marker') ?
      explode(',', $this->getOption('marker')) :
      $config_markers;

    return $option_marker;
  }

}
