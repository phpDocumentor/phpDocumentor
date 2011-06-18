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

/**
 * Parses a method definition.
 *
 * @category   DocBlox
 * @package    Reflection
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Reflection_Method extends DocBlox_Reflection_Function
{
  /** @var bool Remembers whether this method is abstract */
  protected $abstract = false;

  /** @var bool Remembers whether this method is final */
  protected $final = false;

  /** @var bool Remembers whether this method is static */
  protected $static = false;

  /** @var string Remember the visibility of this method; may be either public, protected or private */
  protected $visibility = 'public';

  /**
   * Retrieves the generic information.
   *
   * Finds out whether this method is abstract, static, final and what visibility it has on top of the information
   * found using the DocBlox_Reflection_Function parent method.
   *
   * @param DocBlox_Reflection_TokenIterator $tokens
   *
   * @see DocBlox_Reflection_Function::processGenericInformation
   *
   * @return void
   */
  protected function processGenericInformation(DocBlox_Reflection_TokenIterator $tokens)
  {
    $this->static     = $this->findStatic($tokens)   ? true : false;
    $this->abstract   = $this->findAbstract($tokens) ? true : false;
    $this->final      = $this->findFinal($tokens)    ? true : false;
    $this->visibility = $this->findVisibility($tokens);

    parent::processGenericInformation($tokens);
  }

  /**
   * Returns the visibility for this item.
   *
   * The returned value should match either of the following:
   *
   * * public
   * * protected
   * * private
   *
   * If a method has no visibility set in the class definition this method will return 'public'.
   *
   * @return string
   */
  public function getVisibility()
  {
    return $this->visibility;
  }

  /**
   * Returns whether this method is static.
   *
   * @return bool
   */
  public function isAbstract()
  {
    return $this->abstract;
  }

  /**
   * Returns whether this method is static.
   *
   * @return bool
   */
  public function isStatic()
  {
    return $this->static;
  }

  /**
   * Returns whether this method is final.
   *
   * @return bool
   */
  public function isFinal()
  {
    return $this->final;
  }

  /**
   * Returns the XML representation of this object or false if an error occurred.
   *
   * @return string|boolean
   */
  public function __toXml()
  {
    $xml = new SimpleXMLElement('<method></method>');
    $xml->name         = $this->getName();
    $xml['final']      = $this->isFinal() ? 'true' : 'false';
    $xml['abstract']   = $this->isAbstract() ? 'true' : 'false';
    $xml['static']     = $this->isStatic() ? 'true' : 'false';
    $xml['visibility'] = $this->getVisibility();
    $xml['line']       = $this->getLineNumber();

    $this->addDocblockToSimpleXmlElement($xml);

    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->loadXML($xml->asXML());

    // import methods into class xml
    foreach ($this->arguments as $argument)
    {
      $this->mergeXmlToDomDocument($dom, $argument->__toXml());
    }

    return trim($dom->saveXML());
  }
}