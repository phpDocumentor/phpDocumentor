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
  protected $extends     = false;
  protected $extendsFrom = null;
  protected $implements  = false;
  protected $interfaces  = array();

  protected $constants   = array();
  protected $properties  = array();
  protected $methods     = array();

  protected function extractClassName($tokens)
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

  protected function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    // retrieve generic information about the class
    $this->setName($this->extractClassName($tokens));
    $this->doc_block = $this->findDocBlock($tokens);
    $this->abstract  = $this->findAbstract($tokens) ? true : false;
    $this->final     = $this->findFinal($tokens)    ? true : false;

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

  protected function processConst($tokens)
  {
    $this->resetTimer('const');

    $constant = new DocBlox_Reflection_Constant();
    $constant->parseTokenizer($tokens);
    $constant->setNamespace($this->getNamespace());
    $constant->setNamespaceAliases($this->getNamespaceAliases());
    $this->constants[] = $constant;

    $this->debugTimer('>> Processed class constant '.$constant->getName(), 'const');
  }

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
    $xml['line']       = $this->getLineNumber();
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

}
