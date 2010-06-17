<?php
class Nabu_Reflection_Class extends Nabu_Abstract
{
  protected $name        = '';
  protected $docBlock    = null;
  protected $abstract    = false;
  protected $final       = false;
  protected $extends     = false;
  protected $extendsFrom = null;
  protected $implements  = false;
  protected $interfaces  = array();

  protected $constants   = array();
  protected $properties  = array();
  protected $methods     = array();

  public function parseTokenizer(Nabu_TokenIterator $tokens)
  {
    $this->debug('Started to parse class');
    $docblock          = $tokens->findPreviousByType(T_DOC_COMMENT, 10, array('}'));

    // retrieve generic information about the class
    $this->name       = $tokens->findNextByType(T_STRING, 5, array('{'))->getContent();
    $this->debug('  Name of class is: '.$this->name);

    $this->docBlock   = $docblock ? new Zend_Reflection_Docblock($docblock->getContent()) : '';
    $this->abstract = $tokens->findPreviousByType(T_ABSTRACT, 5, array('}')) ? true : false;
    $this->final    = $tokens->findPreviousByType(T_FINAL, 5, array('}')) ? true : false;

    // parse a EXTENDS section
    $extends = $tokens->gotoNextByType(T_EXTENDS, 5, array('{'));
    $this->extends = ($extends) ? true : false;
    $this->extendsFrom = ($extends) ? $tokens->gotoNextByType(T_STRING, 5, array('{'))->getContent() : null;

    // Parse an eventual implements section: implements _always_ follows extends
    $implements = $tokens->gotoNextByType(T_IMPLEMENTS, 5, array('{'));
    $interfaces = array();
    if ($implements)
    {
      while (($interface_token = $tokens->gotoNextByType(T_STRING, 5, array('{'))) !== false)
      {
        $interfaces[] = $interface_token->getContent();
      }
    }
    $this->implements = ($implements) ? true : false;
    $this->interfaces = $interfaces;

    // register the start and end of this class
    list($start_index, $end_index) = $tokens->getTokenIdsOfBracePair();
    $this->token_start = $start_index;
    $this->token_end = $end_index;

    // parse class contents
    $this->debug('  Parsing class contents');
    $tokens_time = microtime(true);
    while ($tokens->valid() && $tokens->key() <= $end_index)
    {
      $token = $tokens->current();
      switch ($token->getType())
      {
        case T_CONST:
          $time = microtime(true);
          $constant = new Nabu_Reflection_Constant();
          $constant->parseTokenizer($tokens);
          $this->constants[] = $constant;
          $elapsed = microtime(true) - $time;
          $this->debug('  Processed constant '.$constant->getName().' in '.$elapsed.' seconds');
          break;
        case T_VARIABLE:
          $time = microtime(true);
          $property = new Nabu_Reflection_Property();
          $property->parseTokenizer($tokens);
          $this->properties[] = $property;
          $elapsed = microtime(true) - $time;
          $this->debug('  Processed property '.$property->getName().' in '.$elapsed.' seconds');
          break;
        case T_FUNCTION:
          $time = microtime(true);
          $method = new Nabu_Reflection_Method();
          $method->parseTokenizer($tokens);
          $this->methods[] = $method;
          $elapsed = microtime(true) - $time;
          $this->debug('  Processed method '.$method->getName().' in '.$elapsed.' seconds');
          break;
        case '':
          $this->log('CLASS: Literal encountered ('.$tokens->key().'): '.$token->getContent());
          break;
        default:
          $this->log('CLASS: Unhandled token encountered ('.$tokens->key().'): '.$token->getName().(($token->getType() == T_STRING) ? ': '.$token->getContent() : ''), Zend_Log::WARN);
      }

      $tokens->next();
    }
    $tokens_elapsed = microtime(true) - $tokens_time;
    $this->debug('  Processed all tokens in '.$tokens_elapsed.' seconds');
  }

  public function getName()
  {
    return $this->name;
  }

  public function isAbstract()
  {
    return $this->abstract;
  }

  public function isFinal()
  {
    return $this->final;
  }

  public function getDocBlock()
  {
    return $this->docBlock;
  }

  public function getParentClass()
  {
    return $this->extends ? $this->extendsFrom : null;
  }

  public function getParentInterfaces()
  {
    return $this->interfaces;
  }

  public function __toString()
  {
    return $this->getName();
  }

  public function __toXml()
  {
    $xml = new SimpleXMLElement('<class></class>');
    $xml->name         = $this->getName();
    $xml['final']      = $this->isFinal() ? 'true' : 'false';
    $xml['abstract']   = $this->isAbstract() ? 'true' : 'false';
    $xml->extends      = $this->getParentClass();

    foreach ($this->getParentInterfaces() as $interface)
    {
      $xml->addChild('implements', $interface);
    }

    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->loadXML($xml->asXML());

    // import constants into class xml
    foreach ($this->constants as $constant)
    {
      $dom_prop = new DOMDocument();
      $dom_prop->loadXML(trim($constant->__toXml()));

      $xpath = new DOMXPath($dom_prop);
      $qry = $xpath->query('/*');
      for ($i = 0; $i < $qry->length; $i++)
      {
        $dom->documentElement->appendChild($dom->importNode($qry->item($i), true));
      }
    }

    // import properties into class xml
    foreach ($this->properties as $property)
    {
      $dom_prop = new DOMDocument();
      $dom_prop->loadXML(trim($property->__toXml()));

      $xpath = new DOMXPath($dom_prop);
      $qry = $xpath->query('/*');
      for ($i = 0; $i < $qry->length; $i++)
      {
        $dom->documentElement->appendChild($dom->importNode($qry->item($i), true));
      }
    }

    // import methods into class xml
    foreach ($this->methods as $method)
    {
      $dom_method = new DOMDocument();
      $dom_method->loadXML(trim($method->__toXml()));

      $xpath = new DOMXPath($dom_method);
      $qry = $xpath->query('/*');
      for ($i = 0; $i < $qry->length; $i++)
      {
        $dom->documentElement->appendChild($dom->importNode($qry->item($i), true));
      }
    }

    return trim($dom->saveXML());
  }

}