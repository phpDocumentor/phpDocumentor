<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Reflection
 * @subpackage File
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Reflection class for a full file.
 *
 * @category   DocBlox
 * @package    Reflection
 * @subpackage File
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Reflection_File extends DocBlox_Reflection_DocBlockedAbstract
{
  protected $filename           = '';
  protected $hash               = null;
  protected $tokens             = null;
  protected $contents           = '';
  protected $interfaces         = array();
  protected $classes            = array();
  protected $functions          = array();
  protected $constants          = array();
  protected $includes           = array();
  protected $active_namespace   = 'default';
  protected $markers            = array();
  protected $marker_terms       = array('TODO', 'FIXME');

  /**
   * Opens the file and retrieves it's contents.
   *
   * @throws DocBlox_Reflection_Exception when the filename is incorrect or the file can not be opened
   *
   * @param string  $file     Name of the file.
   * @param boolean $validate Whether to check the file using PHP Lint.
   *
   * @return void
   */
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
    $this->name     = $this->filename;
    $this->contents = $this->convertToUtf8($file, file_get_contents($file));
    $this->setHash(filemtime($file));
  }

  /**
   * Converts a piece of text to UTF-8 if it isn't.
   *
   * @param string $contents String to convert.
   *
   * @return string
   */
  protected function convertToUtf8($filename, $contents)
  {
    $encoding   = null;

    // empty files need not be converted (even worse: finfo detects them as binary!)
    if (trim($contents) === '')
    {
      return '';
    }

    // detect encoding and transform to UTF-8
    if (class_exists('finfo'))
    {
      // PHP 5.3 or PECL extension
      $info      = new finfo();
      $encoding  = $info->file($filename, FILEINFO_MIME_ENCODING);
    } elseif(function_exists('mb_detect_encoding'))
    {
      // OR with mbstring
      $encoding = mb_detect_encoding($contents);
    } elseif(function_exists('iconv'))
    {
      // OR using iconv (performance hit)
      $this->log(
        'Neither the finfo nor the mbstring extensions are active; special character handling may '
        . 'not give the best results',
        Zend_Log::WARN
      );
      $encoding = $this->detectEncodingFallback($contents);
    } else
    {
      // or not..
      $this->log(
        'Unable to handle character encoding; finfo, mbstring and iconv extensions are not enabled',
        Zend_Log::CRIT
      );
    }

    // convert if a source encoding is found; otherwise we throw an error and have to continue using the given data
    if (($encoding !== null) && (strtolower($encoding) != 'utf-8'))
    {
      $contents = iconv($encoding, 'UTF-8', $contents);
      if ($contents === false)
      {
        $this->log(
          'Encoding of file ' . $filename . ' from ' . $encoding . ' to UTF-8 failed, please check the notice for a '
            . 'detailed error message',
          Zend_Log::EMERG
        );
      }
    }

    return $contents;
  }

  /**
   * This is a fallback mechanism; if no finfo or mbstring extension are activated this is used.
   *
   * WARNING: try to prevent this; it is assumed that this method is not fool-proof nor performing as well as the other
   * options.
   *
   * @param string $string String to detect the encoding of.
   *
   * @return string Name of the encoding to return.
   */
  private function detectEncodingFallback($string)
  {
    static $list = array('UTF-8', 'ASCII', 'ISO-8859-1', 'UTF-7', 'WINDOWS-1251');

    foreach ($list as $item) {
      $sample = iconv($item, $item, $string);
      if (md5($sample) == md5($string))
        return $item;
    }

    return null;
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

  /**
   * Adds a marker to scan for.
   *
   * @param string $name The Marker term, i.e. FIXME or TODO
   *
   * @return void
   */
  public function addMarker($name)
  {
    $this->marker_terms[] = $name;
  }

  /**
   * Sets a list of markers to search for.
   *
   * @param string[] $markers
   *
   * @see DocBlox_Reflection_File::addMarker()
   *
   * @return void
   */
  public function setMarkers(array $markers)
  {
    foreach($markers as $marker)
    {
      $this->addMarker($marker);
    }
  }

  /**
   * Returns the hash identifying this file.
   *
   * @return string
   */
  public function getHash()
  {
    return $this->hash;
  }

  /**
   * Sets the hash for this file.
   *
   * @param string $hash
   *
   * @return void
   */
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
    $result = null;
    $docblock = $tokens->findNextByType(T_DOC_COMMENT, 10, array(T_CLASS, T_NAMESPACE));

    try
    {
      $result = $docblock ? new DocBlox_Reflection_Docblock($docblock->getContent()) : null;
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

  /**
   * Processes the T_USE token and extracts all namespace aliases.
   *
   * @param DocBlox_TokenIterator $tokens Tokens to interpret with the pointer at the token to be processed.
   *
   * @return void
   */
  protected function processUse(DocBlox_TokenIterator $tokens)
  {
    /** @var DocBlox_Token $token */
    $aliases = array('');
    while(($token = $tokens->next()) && ($token->getContent() != ';'))
    {
      // if a comma is found, go to the next alias
      if (!$token->getType() && $token->getContent() == ',')
      {
        $aliases[] = '';
        continue;
      }

      $aliases[count($aliases)-1] .= $token->getContent();
    }

    $result = array();
    foreach($aliases as $key => $alias)
    {
      // an AS is always surrounded by spaces; by trimming the $alias we then know that the first element is the
      // namespace and the last is the alias.
      // We explicitly do not use spliti to prevent regular expressions for performance reasons (the AS may be any case).
      $alias = explode(' ', trim($alias));

      // if there is only one part, that means no AS is given and the last segment of the namespace functions as
      // alias.
      if (count($alias) == 1)
      {
        $alias_parts = explode('\\', $alias[0]);
        $alias[] = $alias_parts[count($alias_parts)-1];
      }

      $result[$alias[count($alias) -1]] = $alias[0];
      unset($aliases[$key]);
    }

    $this->namespace_aliases = array_merge($this->namespace_aliases, $result);
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
    $interface->setNamespaceAliases($this->namespace_aliases);
    $interface->parseTokenizer($tokens);

    $this->debugTimer('>> Processed interface '.$interface->getName(), 'interface');

    $this->interfaces[$interface->getName()] = $interface;
  }

  protected function processClass(DocBlox_TokenIterator $tokens)
  {
    $this->resetTimer('class');

    $class = new DocBlox_Reflection_Class();
    $class->setNamespace($this->active_namespace);
    $class->setNamespaceAliases($this->namespace_aliases);
    $class->parseTokenizer($tokens);

    $this->debugTimer('>> Processed class '.$class->getName(), 'class');

    $this->classes[$class->getName()] = $class;
  }

  protected function processFunction(DocBlox_TokenIterator $tokens)
  {
    $this->resetTimer('function');

    $function = new DocBlox_Reflection_Function();
    $function->setNamespace($this->active_namespace);
    $function->setNamespaceAliases($this->namespace_aliases);
    $function->parseTokenizer($tokens);

    $this->debugTimer('>> Processed function '.$function->getName(), 'function');

    $this->functions[$function->getName()] = $function;
  }

  protected function processConst(DocBlox_TokenIterator $tokens)
  {
    $this->resetTimer('constant');

    $constant = new DocBlox_Reflection_Constant();
    $constant->setNamespace($this->active_namespace);
    $constant->setNamespaceAliases($this->namespace_aliases);
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

    // add markers
    foreach($this->markers as $marker)
    {
      if (!isset($xml->markers))
      {
        $xml->addChild('markers');
      }

      $marker_obj = $xml->markers->addChild(strtolower($marker[0]), trim($marker[1]));
      $marker_obj->addAttribute('line', $marker[2]);
    }

    // add namespace aliases
    foreach($this->namespace_aliases as $alias => $namespace)
    {
      $alias_obj = $xml->addChild('namespace-alias', $namespace);
      $alias_obj->addAttribute('name', $alias);
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
