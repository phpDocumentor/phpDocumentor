<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Reflection
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

if (!defined('T_NS_SEPARATOR'))
{
  /** @var int This constant is PHP 5.3+, but is necessary for correct parsing */
  define('T_NS_SEPARATOR', 380);
}

/**
 * Parses an interface definition.
 *
 * @category   DocBlox
 * @package    Reflection
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Reflection_Interface extends DocBlox_Reflection_BracesAbstract
{
  /** @var bool Whether this interface extends another */
  protected $extends = false;

  /** @var string|null $extends Where this interface extends from. */
  protected $extendsFrom = null;

  /** @var bool Whether this interface implements another. */
  protected $implements = false;

  /** @var string[] Which interfaces this Interface implements. */
  protected $interfaces = array();

  /** @var DocBlox_Reflection_Constant Which constants are present in this interface */
  protected $constants = array();

  /** @var DocBlox_Reflection_Property Which properties are present in this interface */
  protected $properties = array();

  /** @var DocBlox_Reflection_Method Which methods are present in this interface */
  protected $methods = array();

  /**
   * Retrieve the name of the class starting from the T_CLASS token.
   *
   * @param DocBlox_Token_Iterator $tokens
   *
   * @return string
   */
  protected function extractClassName(DocBlox_Token_Iterator $tokens)
  {
    // a class name can be a combination of a T_NAMESPACE and T_STRING
    $name = '';
    while ($token = $tokens->next())
    {
      if (!in_array($token->type, array(T_WHITESPACE, T_STRING, T_NS_SEPARATOR)))
      {
        $tokens->previous();
        break;
      }

      $name .= $token->content;
    }

    return trim($name);
  }

  /**
   * Extract and store the meta data surrounding a class / interface.
   *
   * @param DocBlox_Token_Iterator $tokens
   *
   * @return void
   */
  protected function processGenericInformation(DocBlox_Token_Iterator $tokens)
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
      } while ($tokens->next()->content == ',');
    }

    $this->implements = ($implements) ? true : false;
    $this->interfaces = $interfaces;
  }

  /**
   * Processes a T_CONST token found inside a class / interface definition.
   *
   * @param DocBlox_Token_Iterator $tokens
   *
   * @return void
   */
  protected function processConst(DocBlox_Token_Iterator $tokens)
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
   * @param DocBlox_Token_Iterator $tokens
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
   * @param DocBlox_Token_Iterator $tokens
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

    $this->methods[$method->getName()] = $method;
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
   * Returns an array of method objects.
   *
   * @return DocBlox_Reflection_Method[]
   */
  public function getMethods()
  {
    return $this->methods;
  }

  /**
   * Returns the method with the given name or null if none is found.
   *
   * @param string $name
   *
   * @return DocBlox_Reflection_Method[]|null
   */
  public function getMethod($name)
  {
    return isset($this->methods[$name]) ? $this->methods[$name] : null;
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