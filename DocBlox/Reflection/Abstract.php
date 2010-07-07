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
  static $token_method_cache = array();

  protected $token_start = 0;
  protected $token_end   = 0;
  protected $line_start  = 0;

  protected $namespace   = 'default';

  public function setNamespace($namespace)
  {
    $this->namespace = $namespace;
  }

  public function getNamespace()
  {
    return $this->namespace;
  }

  protected function findType(DocBlox_TokenIterator $tokens)
  {
    $type = $tokens->findPreviousByType(T_STRING, 5, array(',', '('));
    if (!$type)
    {
      $type = $tokens->findPreviousByType(T_ARRAY, 5, array(',', '('));
    }

    return $type ? $type->getContent() : null;
  }

  public function findDefault(DocBlox_TokenIterator $tokens)
  {
    $default_token        = $tokens->findNextByType(T_STRING, 5, array(',', ')'));
    if (!$default_token)
    {
      $default_token      = $tokens->findNextByType(T_CONSTANT_ENCAPSED_STRING, 5, array(',', ')'));
    }
    if (!$default_token)
    {
      $default_token      = $tokens->findNextByType(T_LNUMBER, 5, array(',', ')'));
    }
    if (!$default_token)
    {
      $default_token      = $tokens->findNextByType(T_ARRAY, 5, array(',', ')'));
    }
    return $default_token ? trim($default_token->getContent(), '\'"') : null;
  }

  public function findAbstract(DocBlox_TokenIterator $tokens)
  {
    return $tokens->findPreviousByType(T_ABSTRACT, 5, array('}'));
  }

  public function findFinal(DocBlox_TokenIterator $tokens)
  {
    return $tokens->findPreviousByType(T_FINAL, 5, array('}'));
  }

  public function findStatic(DocBlox_TokenIterator $tokens)
  {
    return $tokens->findPreviousByType(T_STATIC, 5, array('{', ';'));
  }

  public function findDocBlock(DocBlox_TokenIterator $tokens)
  {
    $docblock = $tokens->findPreviousByType(T_DOC_COMMENT, 10, array('{'. '}', ';'));
    try
    {
      return $docblock ? new Zend_Reflection_Docblock($docblock->getContent()) : null;
    }
    catch (Exception $e)
    {
      $this->log($e->getMessage(), Zend_Log::ERR);
    }
  }

  protected function findVisibility(DocBlox_TokenIterator $tokens)
  {
    $result = 'public';
    $result = $tokens->findPreviousByType(T_PRIVATE, 5, array('{', ';')) ? 'private' : $result;
    $result = $tokens->findPreviousByType(T_PROTECTED, 5, array('{', ';')) ? 'protected' : $result;

    return $result;
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

  protected function processTokens(DocBlox_TokenIterator $tokens)
  {
    return array($tokens->key(), $tokens->key());
  }

  public function parseTokenizer(DocBlox_TokenIterator $tokens)
  {
    if (!$tokens->current())
    {
      $this->debug('  No tokens found to parse');
      return;
    }

    $this->debug('  Started to parse '.$tokens->current()->getName());
    $this->line_start = $tokens->current()->getLineNumber();

    // retrieve generic information about the class
    $this->processGenericInformation($tokens);

    list($start, $end) = $this->processTokens($tokens);
    $this->token_start = $start;
    $this->token_end   = $end;

    $this->debug('    Manually determined method range token ids at '.$start.'->'.$end);

    $this->debugTimer('    Processed all tokens');
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

  public function addDocblockToSimpleXmlElement(SimpleXMLElement $xml)
  {
    if ($this->getDocBlock())
    {
      if (!isset($xml->docblock))
      {
        $xml->addChild('docblock');
      }
      $xml->docblock->description = str_replace(PHP_EOL, '<br/>', $this->getDocBlock()->getShortDescription());
        $xml->docblock->{'long-description'} = str_replace(PHP_EOL, '<br/>', $this->getDocBlock()->getLongDescription());

      /** @var Zend_Reflection_Docblock_Tag $tag */
      foreach ($this->getDocBlock()->getTags() as $tag)
      {
        $tag_object = $xml->docblock->addChild('tag', htmlspecialchars($tag->getDescription()));
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

  public function mergeDomDocuments(DOMDocument $origin, DOMDocument $document)
  {
    $xpath = new DOMXPath($document);
    $qry = $xpath->query('/*');
    for ($i = 0; $i < $qry->length; $i++)
    {
      $origin->documentElement->appendChild($origin->importNode($qry->item($i), true));
    }
  }

  abstract public function __toXml();

}
