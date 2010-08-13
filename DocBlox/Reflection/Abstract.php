<?php
/**
 * @author    mvriel
 * @copyright
 */

/**
 * Provide a short description for this class.
 *
 * @author     mvriel
 * @package
 * @subpackage
 */
abstract class DocBlox_Reflection_Abstract extends DocBlox_Abstract
{
  /**
   * Stores the method name of the processing method for a token.
   *
   * The generation of method names may be a performance costly task and is quite often executed.
   * As such we cache the method names which are coming from tokens here in this array.
   *
   * @var string[]
   */
  private static $token_method_cache = array();

  /**
   * Stores the name for this Reflection object.
   *
   * @var string
   */
  protected $name      = 'Unknown';

  /**
   * Stores the start position by token index.
   *
   * @var int
   */
  protected $token_start = 0;

  /**
   * Stores the end position by token index.
   *
   * @var int
   */
  protected $token_end   = 0;

  /**
   * Stores the line where the initial token was found.
   *
   * @var int
   */
  protected $line_start  = 0;

  /**
   * Stores the name of the namespace to which this belongs.
   *
   * @var string
   */
  protected $namespace   = 'default';


  /**
   * Sets the name for this Reflection Object.
   *
   * @throws InvalidArgumentException
   * @param  string $name
   * @return void
   */
  public function setName($name)
  {
    if (!is_string($name))
    {
      throw new InvalidArgumentException('Expected name to be a string');
    }

    $this->name = $name;
  }

  /**
   * Returns the name for this Reflection object.
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Sets the name of the namespace to which this belongs.
   *
   * @throws InvalidArgumentException
   * @param  $namespace
   * @return void
   */
  public function setNamespace($namespace)
  {
    if (!is_string($namespace))
    {
      throw new InvalidArgumentException('Expected the namespace to be a string');
    }

    $this->namespace = $namespace;
  }

  /**
   * Returns the name of the namespace to which this belongs.
   *
   * @return string
   */
  public function getNamespace()
  {
    return $this->namespace;
  }

  /**
   * Find the Type for this object.
   *
   * Please note that the iterator cursor does not change due to this method
   *
   * @param  DocBlox_TokenIterator $tokens
   * @return string|null
   */
  protected function findType(DocBlox_TokenIterator $tokens)
  {
    // first see if there is a string at most 5 characters back
    $type = $tokens->findPreviousByType(T_STRING, 5, array(',', '('));

    // if none found, check if there is an array at most 5 places back
    if (!$type)
    {
      $type = $tokens->findPreviousByType(T_ARRAY, 5, array(',', '('));
    }

    // if anything is found, return the content
    return $type ? $type->getContent() : null;
  }

  /**
   * Find the Default value for this object.
   *
   * Usually used with variables or arguments.
   * Please note that the iterator cursor does not change due to this method
   *
   * @param  DocBlox_TokenIterator $tokens
   * @return string|null
   */
  protected function findDefault(DocBlox_TokenIterator $tokens)
  {
    // check if a string is found
    $default_token        = $tokens->findNextByType(T_STRING, 5, array(',', ')'));
    if (!$default_token)
    {
      // check for a constant
      $default_token      = $tokens->findNextByType(T_CONSTANT_ENCAPSED_STRING, 5, array(',', ')'));
    }
    if (!$default_token)
    {
      // check for a number
      $default_token      = $tokens->findNextByType(T_LNUMBER, 5, array(',', ')'));
    }
    if (!$default_token)
    {
      // check for an array definition
      $default_token      = $tokens->findNextByType(T_ARRAY, 5, array(',', ')'));
    }

    // remove any surrounding single or double quotes before returning the data
    return $default_token ? trim($default_token->getContent(), '\'"') : null;
  }

  /**
   * Determine whether this token has the abstract keyword.
   *
   * Please note that the iterator cursor does not change due to this method
   *
   * @param  DocBlox_TokenIterator $tokens
   * @return DocBlox_Token|null
   */
  protected function findAbstract(DocBlox_TokenIterator $tokens)
  {
    return $tokens->findPreviousByType(T_ABSTRACT, 5, array('}'));
  }

  /**
   * Determine whether this token has the final keyword.
   *
   * Please note that the iterator cursor does not change due to this method
   *
   * @param  DocBlox_TokenIterator $tokens
   * @return DocBlox_Token|null
   */
  protected function findFinal(DocBlox_TokenIterator $tokens)
  {
    return $tokens->findPreviousByType(T_FINAL, 5, array('}'));
  }

  /**
   * Determine whether this token has the static keyword.
   *
   * Please note that the iterator cursor does not change due to this method
   *
   * @param  DocBlox_TokenIterator $tokens
   * @return DocBlox_Token|null
   */
  protected function findStatic(DocBlox_TokenIterator $tokens)
  {
    return $tokens->findPreviousByType(T_STATIC, 5, array('{', ';'));
  }

  /**
   * Returns the first docblock preceding the active token within 10 tokens.
   *
   * Please note that the iterator cursor does not change due to this method
   *
   * @param  DocBlox_TokenIterator $tokens
   * @return Zend_Reflection_DocBlock|null
   */
  protected function findDocBlock(DocBlox_TokenIterator $tokens)
  {
    $result = null;
    $docblock = $tokens->findPreviousByType(T_DOC_COMMENT, 10, array('{'. '}', ';'));
    try
    {
      $result = $docblock ? new Zend_Reflection_Docblock($docblock->getContent()) : null;
    }
    catch (Exception $e)
    {
      $this->log($e->getMessage(), Zend_Log::CRIT);
    }

    if (!$result)
    {
      $this->log('No DocBlock was found for '.substr(get_class($this), strrpos(get_class($this), '_')+1).' '.$this->getName().' on line '.$this->getLineNumber(), Zend_Log::ERR);
    }

    return $result;
  }

  protected function findVisibility(DocBlox_TokenIterator $tokens)
  {
    $result = 'public';
    $result = $tokens->findPreviousByType(T_PRIVATE, 5, array('{', ';')) ? 'private' : $result;
    $result = $tokens->findPreviousByType(T_PROTECTED, 5, array('{', ';')) ? 'protected' : $result;

    return $result;
  }

  protected function processTokens(DocBlox_TokenIterator $tokens)
  {
    return array($tokens->key(), $tokens->key());
  }

  protected function processToken(DocBlox_Token $token, DocBlox_TokenIterator $tokens)
  {
    static $token_method_exists_cache = array();

    // cache method name; I expect to find this a lot
    $token_name = $token->getName();
    if (!isset(self::$token_method_cache[$token_name]))
    {
      self::$token_method_cache[$token_name] = 'process'.str_replace(' ', '', ucwords(strtolower(substr(str_replace('_', ' ', $token_name), 2))));
    }

    // cache the method_exists calls to speed up processing
    $method_name = self::$token_method_cache[$token_name];
    if (!isset($token_method_exists_cache[$method_name]))
    {
      $token_method_exists_cache[$method_name] = method_exists($this, $method_name);
    }

    // if method exists; parse the token
    if ($token_method_exists_cache[$method_name])
    {
      $this->$method_name($tokens);
    }
  }

  abstract protected function processGenericInformation(DocBlox_TokenIterator $tokens);

  public function parseTokenizer(DocBlox_TokenIterator $tokens)
  {
    if (!$tokens->current())
    {
      $this->log('>> No contents found to parse');
      return;
    }

    $this->debug('== Parsing token '.$tokens->current()->getName());
    $this->line_start = $tokens->current()->getLineNumber();

    // retrieve generic information about the class
    $this->processGenericInformation($tokens);

    list($start, $end) = $this->processTokens($tokens);
    $this->token_start = $start;
    $this->token_end   = $end;

    $this->debug('== Determined token index range to be '.$start.' => '.$end);

    $this->debugTimer('>> Processed all tokens');
  }

  public function getStartTokenId()
  {
    return $this->token_start;
  }

  public function getLineNumber()
  {
    return $this->line_start;
  }

  public function getEndTokenId()
  {
    return $this->token_end;
  }

  protected function addDocblockToSimpleXmlElement(SimpleXMLElement $xml)
  {
    if ($this->getDocBlock())
    {
      if (!isset($xml->docblock))
      {
        $xml->addChild('docblock');
      }
      $xml->docblock->description = utf8_encode(str_replace(PHP_EOL, '<br/>', $this->getDocBlock()->getShortDescription()));
        $xml->docblock->{'long-description'} = utf8_encode(str_replace(PHP_EOL, '<br/>', $this->getDocBlock()->getLongDescription()));

      /** @var Zend_Reflection_Docblock_Tag $tag */
      foreach ($this->getDocBlock()->getTags() as $tag)
      {
        $tag_object = $xml->docblock->addChild('tag', utf8_encode(htmlspecialchars($tag->getDescription())));
        $tag_object['name'] = trim($tag->getName(), '@');
        if (method_exists($tag, 'getType'))
        {
          $tag_object['type'] = $tag->getType();
        }
        if (method_exists($tag, 'getVariableName'))
        {
          $tag_object['variable'] = $tag->getVariableName();
        }
      }
    }
  }

  protected function mergeXmlToDomDocument(DOMDocument $origin, $xml)
  {
    $dom_arguments = new DOMDocument();
    $dom_arguments->loadXML(trim($xml));

    $this->mergeDomDocuments($origin, $dom_arguments);
  }

  protected function mergeDomDocuments(DOMDocument $origin, DOMDocument $document)
  {
    $xpath = new DOMXPath($document);
    $qry = $xpath->query('/*');
    for ($i = 0; $i < $qry->length; $i++)
    {
      $origin->documentElement->appendChild($origin->importNode($qry->item($i), true));
    }
  }

  abstract public function __toXml();

  public function getDocBlock()
  {
    return false;
  }

}
