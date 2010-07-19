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
class DocBlox_Reflection_Interface extends DocBlox_Reflection_BracesAbstract
{
  protected $doc_block    = null;
  protected $extends     = false;
  protected $extendsFrom = null;
  protected $implements  = false;
  protected $interfaces  = array();

  protected $constants   = array();
  protected $properties  = array();
  protected $methods     = array();

  public function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    // retrieve generic information about the class
    $this->setName($tokens->findNextByType(T_STRING, 5, array('{'))->getContent());
    $this->doc_block = $this->findDocBlock($tokens);
    $this->abstract  = $this->findAbstract($tokens) ? true : false;
    $this->final     = $this->findFinal($tokens)    ? true : false;

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
  }

  protected function processConst($tokens)
  {
    $this->resetTimer('const');

    $constant = new DocBlox_Reflection_Constant();
    $constant->parseTokenizer($tokens);
    $this->constants[] = $constant;

    $this->debugTimer('>> Processed class constant '.$constant->getName(), 'const');
  }

  protected function processVariable($tokens)
  {
    $this->resetTimer('variable');

    $property = new DocBlox_Reflection_Property();
    $property->parseTokenizer($tokens);
    $this->properties[] = $property;

    $this->debugTimer('>> Processed property '.$property->getName(), 'variable');
  }

  protected function processFunction($tokens)
  {
    $this->resetTimer('method');

    $method = new DocBlox_Reflection_Method();
    $method->parseTokenizer($tokens);
    $this->methods[] = $method;
    $this->debugTimer('>>  Processed method '.$method->getName(), 'method');
  }

  /**
   * Returns the docblock associated with this class; if any
   *
   * @return Zend_Reflection_Docblock|null
   */
  public function getDocBlock()
  {
    return $this->doc_block;
  }

  public function getParentClass()
  {
    return $this->extends ? $this->extendsFrom : null;
  }

  public function getParentInterfaces()
  {
    return $this->interfaces;
  }

  public function __toXml(SimpleXMLElement $xml = null)
  {
    if ($xml === null)
    {
      $xml = new SimpleXMLElement('<interface></interface>');
    }
    $xml->name         = $this->getName();
    $xml['namespace']  = $this->getNamespace();
    $xml->extends      = $this->getParentClass();

    $this->addDocblockToSimpleXmlElement($xml);

    foreach ($this->getParentInterfaces() as $interface)
    {
      $xml->addChild('implements', $interface);
    }

    $dom = new DOMDocument('1.0');
    $dom->loadXML($xml->asXML());

    foreach ($this->constants as $constant)
    {
      $this->mergeXmlToDomDocument($dom, $constant->__toXml());
    }
    foreach ($this->properties as $property)
    {
      $this->mergeXmlToDomDocument($dom, $property->__toXml());
    }
    foreach ($this->methods as $method)
    {
      $this->mergeXmlToDomDocument($dom, $method->__toXml());
    }

    return trim($dom->saveXML());
  }

  public function __toString()
  {
    return $this->getName();
  }

}
