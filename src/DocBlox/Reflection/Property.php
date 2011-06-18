<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Reflection
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Reflection class for a the property in a class.
 *
 * @category DocBlox
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Reflection_Property extends DocBlox_Reflection_Variable
{
  /** @var bool Remembers whether this property is static */
  protected $static = false;

  /** @var bool Remembers whether this property is final */
  protected $final = false;

  /** @var string Remember the visibility of this property; may be either public, protected or private */
  protected $visibility = 'public';

  /**
   * Retrieves the generic information.
   *
   * Finds out whether this property is static, final and what visibility it has on top of the information found using
   * the DocBlox_Reflection_Variable parent method.
   *
   * @param DocBlox_Reflection_TokenIterator $tokens
   *
   * @see DocBlox_Reflection_Variable::processGenericInformation
   *
   * @return void
   */
  protected function processGenericInformation(DocBlox_Reflection_TokenIterator $tokens)
  {
    $this->static     = $this->findStatic($tokens) ? true : false;
    $this->final      = $this->findFinal($tokens)  ? true : false;
    $this->visibility = $this->findVisibility($tokens);

    parent::processGenericInformation($tokens);
  }

  /**
   * Returns whether this property is static.
   *
   * @return bool
   */
  public function isStatic()
  {
    return $this->static;
  }

  /**
   * Returns whether this property is final.
   *
   * @return bool
   */
  public function isFinal()
  {
    return $this->final;
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
   * If a property has no visibility set in the class definition this method will return 'public'.
   *
   * @return string
   */
  public function getVisibility()
  {
    return $this->visibility;
  }

  /**
   * Returns the XML representation of this object or false if an error occurred.
   *
   * @param SimpleXMLElement $xml If not null, expands the given SimpleXML Node instead of instantiating a new one.
   *
   * @return string|boolean
   */
  public function __toXml(SimpleXMLElement $xml = null)
  {
    if ($xml === null)
    {
      $xml = new SimpleXMLElement('<property></property>');
    }

    $xml['final']      = $this->isFinal() ? 'true' : 'false';
    $xml['static']     = $this->isStatic() ? 'true' : 'false';
    $xml['visibility'] = $this->getVisibility();
    $xml['line']       = $this->getLineNumber();

    return parent::__toXml($xml);
  }
}