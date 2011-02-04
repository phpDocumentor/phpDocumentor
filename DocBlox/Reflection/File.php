<?php
class DocBlox_Reflection_File extends DocBlox_Reflection_DocBlockedAbstract
{
  protected $filename         = '';
  protected $hash             = null;
  protected $tokens           = null;
  protected $contents         = '';
  protected $interfaces       = array();
  protected $classes          = array();
  protected $functions        = array();
  protected $constants        = array();
  protected $includes         = array();
  protected $active_namespace = 'default';
  protected $markers          = array();
  protected $marker_terms     = array('TODO', 'FIXME');

  public function __construct($file, $validate)
  {
    parent::__construct();

    if (!is_string($file) || (!is_readable($file)))
    {
      throw new DocBlox_Reflection_Exception('The given file should be a string, should exist on the filesystem and should be readable');
    }

    if ($validate)
    {
      exec('php -l '.escapeshellarg($file), $output, $result);
      if ($result != 0)
      {
        throw new DocBlox_Reflection_Exception('The given file could not be interpreted as it contains errors: '.implode(PHP_EOL, $output));
      }
    }

    $this->setFilename($file);
    $this->name = $this->filename;
    $contents = file_get_contents($file);

    // detect encoding and transform to UTF-8
    $info = new finfo();
    $mime = $info->file($file, FILEINFO_MIME);
    $mime_info = explode('=', $mime);
    if (strtolower($mime_info[1]) != 'utf-8')
    {
      $contents = iconv($mime_info[1], 'UTF-8', $contents);
    }

    $this->contents = $contents;
    $this->setHash(filemtime($file));
  }

  public function addMarker($name)
  {
    $this->marker_terms[] = $name;
  }

  /**
   * Sets the file name for this file.
   *
   * @param string $filename
   *
   * @return void
   */
  public function setFilename($filename)
  {
    $this->filename = $filename;
  }

  public function setMarkers(array $markers)
  {
    $this->marker_terms = $markers;
  }

  public function getHash()
  {
    return $this->hash;
  }

  public function setHash($hash)
  {
    $this->hash = $hash;
  }

  public function initializeTokens()
  {
    if ($this->tokens instanceof DocBlox_TokenIterator)
    {
      return;
    }

    $this->tokens = token_get_all($this->contents);
    $this->debug(count($this->tokens).' tokens found in class '.$this->getName());
    $this->tokens = new DocBlox_TokenIterator($this->tokens);
  }

  public function process()
  {
    $this->initializeTokens();
    $this->parseTokenizer($this->tokens);

    // preserve memory by unsetting the $this->tokens
    unset($this->tokens);
  }

  protected function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    // find file docblock; standard function does not suffice as this scans backwards and we have to make sure it isn't
    // the docblock of another element
    $this->doc_block = $this->findDocBlock($tokens);

    $result = array();
    // find all markers, get the entire line
    foreach(explode("\n", $this->contents) as $line_number => $line)
    {
      preg_match_all('~//[\s]*('.implode('|', $this->marker_terms).')\:?[\s]*(.*)~', $line, $matches, PREG_SET_ORDER);
      foreach ($matches as &$match)
      {
        $match[3] = $line_number+1;
      }
      $result = array_merge($result, $matches);
    }
    // store marker results and remove first entry (entire match), this results in an array with 2 entries:
    // marker name and content
    $this->markers = $result;
    foreach($this->markers as &$marker)
    {
      array_shift($marker);
    }
  }

  public function findDocBlock(DocBlox_TokenIterator $tokens)
  {
    $docblock = $tokens->findNextByType(T_DOC_COMMENT, 10, array(T_CLASS, T_NAMESPACE));

    try
    {
      $result = $docblock ? new Zend_Reflection_Docblock($docblock->getContent()) : null;
    }
    catch (Exception $e)
    {
      $this->log($e->getMessage(), Zend_Log::CRIT);
    }

    // TODO: add a check if a class immediately follows this docblock, if so this is not a page level docblock but a class docblock

    // even with a docblock at top (which may belong to some other component) does it
    // need to have a package tag to classify
    if ($result && !$result->hasTag('package'))
    {
      $result = null;
    }

    if (!$result)
    {
      $this->log('No Page-level DocBlock was found for '.$this->getName(), Zend_Log::ERR);
    }

    return $result;
  }

  public function processTokens(DocBlox_TokenIterator $tokens)
  {
    $token = null;
    while ($tokens->valid())
    {
      $token = $token === null ? $tokens->current() : $tokens->next();

      if ($token && $token->getType())
      {
        $this->processToken($token, $tokens);
      }
    }
  }

  protected function processNamespace(DocBlox_TokenIterator $tokens)
  {
    // collect all namespace parts
    $namespace = array();
    while($token = $tokens->gotoNextByType(T_STRING, 5, array(';', '{')))
    {
      $namespace[] = $token->getContent();
    }
    $namespace = implode('\\', $namespace);

    $this->active_namespace = $namespace;
  }

  protected function processInterface(DocBlox_TokenIterator $tokens)
  {
    $this->resetTimer('interface');

    $interface = new DocBlox_Reflection_Interface();
    $interface->setNamespace($this->active_namespace);
    $interface->parseTokenizer($tokens);

    $this->debugTimer('>> Processed interface '.$interface->getName(), 'interface');

    $this->interfaces[$interface->getName()] = $interface;
  }

  protected function processClass(DocBlox_TokenIterator $tokens)
  {
    $this->resetTimer('class');

    $class = new DocBlox_Reflection_Class();
    $class->setNamespace($this->active_namespace);
    $class->parseTokenizer($tokens);

    $this->debugTimer('>> Processed class '.$class->getName(), 'class');

    $this->classes[$class->getName()] = $class;
  }

  protected function processFunction(DocBlox_TokenIterator $tokens)
  {
    $this->resetTimer('function');

    $function = new DocBlox_Reflection_Function();
    $function->setNamespace($this->active_namespace);
    $function->parseTokenizer($tokens);

    $this->debugTimer('>> Processed function '.$function->getName(), 'function');

    $this->functions[$function->getName()] = $function;
  }

  protected function processConst(DocBlox_TokenIterator $tokens)
  {
    $this->resetTimer('constant');

    $constant = new DocBlox_Reflection_Constant();
    $constant->setNamespace($this->active_namespace);
    $constant->parseTokenizer($tokens);

    $this->debugTimer('>> Processed constant '.$constant->getName(), 'constant');

    $this->constants[$constant->getName()] = $constant;
  }

  protected function processRequire(DocBlox_TokenIterator $tokens)
  {
    $this->processInclude($tokens);
  }

  protected function processRequireOnce(DocBlox_TokenIterator $tokens)
  {
    $this->processInclude($tokens);
  }

  protected function processIncludeOnce(DocBlox_TokenIterator $tokens)
  {
    $this->processInclude($tokens);
  }

  protected function processInclude(DocBlox_TokenIterator $tokens)
  {
    $this->resetTimer('include');

    $include = new DocBlox_Reflection_Include();
    $include->setNamespace($this->active_namespace);
    $include->parseTokenizer($tokens);

    $this->debugTimer('>> Processed constant '.$include->getName(), 'include');

    $this->includes[] = $include;
  }

  /**
   *
   * @return bool|string
   */
  public function __toXml()
  {
    $xml = new SimpleXMLElement('<file path="'.ltrim($this->filename, './').'" hash="'.$this->hash.'"></file>');
    $this->addDocblockToSimpleXmlElement($xml);

    foreach($this->markers as $marker)
    {
      if (!isset($xml->markers))
      {
        $xml->addChild('markers');
      }

      $marker_obj = $xml->markers->addChild(strtolower($marker[0]), trim($marker[1]));
      $marker_obj->addAttribute('line', $marker[2]);
    }

    $dom = new DOMDocument('1.0', 'utf-8');
    $dom->loadXML(trim($xml->asXML()));

    foreach($this->includes as $include)
    {
      $this->mergeXmlToDomDocument($dom, trim($include->__toXml()));
    }
    foreach($this->constants as $constant)
    {
      $this->mergeXmlToDomDocument($dom, trim($constant->__toXml()));
    }
    foreach($this->functions as $function)
    {
      $this->mergeXmlToDomDocument($dom, trim($function->__toXml()));
    }
    foreach($this->interfaces as $interface)
    {
      $this->mergeXmlToDomDocument($dom, trim($interface->__toXml()));
    }
    foreach($this->classes as $class)
    {
      $this->mergeXmlToDomDocument($dom, trim($class->__toXml()));
    }

    return trim($dom->saveXml());
  }
}
