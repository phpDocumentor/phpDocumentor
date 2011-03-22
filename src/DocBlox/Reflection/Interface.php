<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Static_Reflection
 * @copyright  Copyright (c) 2010-2010 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Parses an interface definition.
 *
 * @category   DocBlox
 * @package    Static_Reflection
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Reflection_Interface extends DocBlox_Reflection_BracesAbstract
{
  protected $extends     = false;
  protected $extendsFrom = null;
  protected $implements  = false;
  protected $interfaces  = array();

  protected $constants   = array();
  protected $properties  = array();
  protected $methods     = array();

  /**
   * Retrieve the name of the class starting from the T_CLASS token.
   *
   * @param DocBlox_TokenIterator $tokens
   *
   * @return string
   */
  protected function extractClassName(DocBlox_TokenIterator $tokens)
  {
    // a class name can be a combination of a T_NAMESPACE and T_STRING
    $name = '';
    while ($token = $tokens->next())
    {
      if (!in_array($token->getType(), array(T_WHITESPACE, T_STRING, T_NS_SEPARATOR)))
      {
        $tokens->previous();
        break;
      }

      $name .= $token->getContent();
    }

    return trim($name);
  }

  /**
   * Extract and store the meta data surrounding a class / interface.
   *
   * @param DocBlox_TokenIterator $tokens
   *
   * @return void
   */
  protected function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    // retrieve generic information about the class
    $this->setName($this->extractClassName($tokens));
    $this->doc_block = $this->findDocBlock($tokens);

    // parse a EXTENDS section
    $extends = $tokens->gotoNextByType(T_EXTENDS, 5, array('{'));
    $this->extends = ($extends) ? true : false;
    $this->extendsFrom = ($extends) ? $this->extractClassName($tokens) : null;

    // Parse an eventual implements section: implements _always_ follows extends
    $implements = $tokens->gotoNextByType(T_IMPLEMENTS, 5, array('{'));
    $interfaces = array();
    if ($implements)
    {
      do
      {
        $interfaces[] = $this->extractClassName($tokens);
      } while ($tokens->next()->getContent() == ',');
    }

    $this->implements = ($implements) ? true : false;
    $this->interfaces = $interfaces;
  }

  /**
   * Processes a T_CONST token found inside a class / interface definition.
   *
   * @param DocBlox_TokenIterator $tokens
   *
   * @return void
   */
  protected function processConst(DocBlox_TokenIterator $tokens)
  {
    $this->resetTimer('const');

    $constant = new DocBlox_Reflection_Constant();
    $constant->parseTokenizer($tokens);
    $constant->setNamespace($this->getNamespace());
    $constant->setNamespaceAliases($this->getNamespaceAliases());
    $this->constants[] = $constant;

    $this->debugTimer('>> Processed class constant '.$constant->getName(), 'const');
  }

  /**
   * Processes a T_VARIABLE token found inside a class / interface definition.
   *
   * @param DocBlox_TokenIterator $tokens
   *
   * @return void
   */
  protected function processVariable($tokens)
  {
    $this->resetTimer('variable');

    $property = new DocBlox_Reflection_Property();
    $property->parseTokenizer($tokens);
    $property->setNamespace($this->getNamespace());
    $property->setNamespaceAliases($this->getNamespaceAliases());
    $this->properties[] = $property;

    $this->debugTimer('>> Processed property '.$property->getName(), 'variable');
  }

  /**
   * Processes a T_FUNCTION token found inside a class / interface definition.
   *
   * @param DocBlox_TokenIterator $tokens
   *
   * @return void
   */
  protected function processFunction($tokens)
  {
    $this->resetTimer('method');

    $method = new DocBlox_Reflection_Method();
    $method->parseTokenizer($tokens);
    $method->setNamespace($this->getNamespace());
    $method->setNamespaceAliases($this->getNamespaceAliases());
    $this->methods[] = $method;
    $this->debugTimer('>>  Processed method '.$method->getName(), 'method');
  }

  /**
   * Returns the name of the superclass.
   *
   * @return string|null
   */
  public function getParentClass()
  {
    return $this->extends ? $this->extendsFrom : null;
  }

  /**
   * Returns the names of the implemented interfaces.
   *
   * @return string[]
   */
  public function getParentInterfaces()
  {
    return $this->interfaces;
  }

  /**
   * Convert this definition to an XML element.
   *
   * @param null|SimpleXMLElement $xml
   *
   * @return string
   */
  public function __toXml(SimpleXMLElement $xml = null)
  {
    if ($xml === null)
    {
      $xml = new SimpleXMLElement('<interface></interface>');
    }

    $xml->name         = $this->getName();
    $xml['namespace']  = $this->getNamespace();
    $xml['line']       = $this->getLineNumber();
    $xml->extends      = $this->getParentClass() ? $this->expandType($this->getParentClass()) : '';
    $xml->full_name    = $this->expandType($this->getName());

    $this->addDocblockToSimpleXmlElement($xml);

    foreach ($this->getParentInterfaces() as $interface)
    {
      $xml->addChild('implements', $this->expandType($interface));
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

}