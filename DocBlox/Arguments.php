<?php
class DocBlox_Arguments extends Zend_Console_Getopt
{
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
  protected $allowed_extensions = array('php', 'php3', 'phtml');

  /**
   * Initializes the object with all supported parameters.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct(array(
      'h|help'         => 'Show this help message',
      'f|filename=s'   => 'Comma-separated list of files to parse. The wildcards ? and * are supported',
      'd|directory=s'  => 'Comma-separated list of directories to (recursively) parse.',
      'e|extensions-s' => 'Optional comma-separated list of extensions to parse, defaults to php, php3 and phtml',
      't|target-s'     => 'Path where to store the generated output (optional, defaults to "output")',
      'v|verbose'      => 'Provides additional information during parsing, usually only needed for debuggin purposes',
      'i|ignore-s'     => 'Comma-separated list of file(s) and directories that will be ignored. Wildcards * and ? are supported',
      'm|markers-s'    => 'Comma-separated list of markers/tags to filter, (optional, defaults to: "TODO,FIXME")',
      'force'          => 'Forces a full build of the documentation, does not increment existing documentation',
    ));
  }

  public function getUsageMessage()
  {
    $usage = "Usage: {$this->_progname}\n";
    $maxLen = 20;
    foreach ($this->_rules as $rule) {
        $flags = array();
        if (is_array($rule['alias'])) {
            foreach ($rule['alias'] as $flag) {
                $flags[] = (strlen($flag) == 1 ? '-' : '--') . $flag;
            }
        }
        $linepart['name'] = implode(' [', $flags).(count($flags) > 1 ? ']' : '');
        if (isset($rule['param']) && $rule['param'] != 'none') {
            $linepart['name'] .= ' ';
            $rule['paramType'] = strtoupper($rule['paramType']);
            switch ($rule['param']) {
                case 'optional':
                    $linepart['name'] .= "[{$rule['paramType']}]";
                    break;
                case 'required':
                    $linepart['name'] .= "{$rule['paramType']}";
                    break;
            }
        }
        if (strlen($linepart['name']) > $maxLen) {
            $maxLen = strlen($linepart['name']);
        }
        $linepart['help'] = '';
        if (isset($rule['help'])) {
            $linepart['help'] .= $rule['help'];
        }
        $lines[] = $linepart;
    }
    foreach ($lines as $linepart) {
        $usage .= sprintf("%s %s\n",
        str_pad($linepart['name'], $maxLen),
        $linepart['help']);
    }
    return $usage;
  }

  /**
   * Interprets the -d and -f options and retrieves all filenames.
   *
   * This method does take the extension option into account but _not_ the
   * ignore list. The ignore list is handled in the parser.
   *
   * @todo method contains duplicate code, refactor
   * @todo consider moving the filtering on ignore_paths here
   * @return string[]
   */
  protected function parseFiles()
  {
    $files = array();

    // read the filename argument in search for files (wildcards are explicitly allowed)
    $expressions = $this->getOption('filename') ? explode(',', $this->getOption('filename')) : array();
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

    // get all directories which must be recursively checked
    $option_directories = $this->getOption('directory') ? explode(',', $this->getOption('directory')) : array();
    foreach ($option_directories as $directory)
    {
      // if the given is not a directory, skip it
      if (!is_dir($directory))
      {
        continue;
      }

      // get all files recursively to the files array
      $files_iterator = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
      /** @var SplFileInfo $file */
      foreach(new RecursiveIteratorIterator($files_iterator) as $file)
      {
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
    if ($this->getOption('extensions'))
    {
      $this->allowed_extensions = explode(',', $this->getOption('extensions'));
    }

    return $this->allowed_extensions;
  }

  /**
   * Retrieves the path to save the result to.
   *
   * @throws Exception
   * @return string
   */
  public function getTarget()
  {
    if (!$this->getOption('target'))
    {
      return './output';
    }

    $target = trim($this->getOption('target'));
    if (($target == '') || ($target == '/'))
    {
      throw new Exception('Either an empty path or root was given');
    }

    return $target;
  }

  /**
   * Returns all ignore patterns.
   *
   * @todo consider moving the conversion from glob to regex to here.
   * @return array
   */
  public function getIgnorePatterns()
  {
    if (!$this->getOption('ignore'))
    {
      return array();
    }

    return explode(',', $this->getOption('ignore'));
  }

  /**
   * Returns the list of markers to scan for and summize in their separate page.
   *
   * @return string[]
   */
  public function getMarkers()
  {
    if (!$this->getOption('markers'))
    {
      return array('TODO', 'FIXME');
    }

    return explode(',', $this->getOption('markers'));
  }
}
