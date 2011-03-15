<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Static_Reflection
 * @copyright  Copyright (c) 2010-2010 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Parses a class definition.
 *
 * @category   DocBlox
 * @package    Static_Reflection
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Reflection_Class extends DocBlox_Reflection_Interface
{
  protected $abstract    = false;
  protected $final       = false;

  /**
   * Extract and store the meta data surrounding a class.
   *
   * @param DocBlox_TokenIterator $tokens
   *
   * @return void
   */
  protected function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    // retrieve generic information about the class
    $this->abstract  = $this->findAbstract($tokens) ? true : false;
    $this->final     = $this->findFinal($tokens)    ? true : false;

    parent::processGenericInformation($tokens);
  }

  /**
   * Returns whether this class definition is abstract.
   *
   * @return bool
   */
  public function isAbstract()
  {
    return $this->abstract;
  }

  /**
   * Returns whether this class definition is final.
   *
   * @return bool
   */
  public function isFinal()
  {
    return $this->final;
  }

  public function __toXml(SimpleXMLElement $xml = null)
  {
    $xml = new SimpleXMLElement('<class></class>');
    $xml['final']      = $this->isFinal() ? 'true' : 'false';
    $xml['abstract']   = $this->isAbstract() ? 'true' : 'false';
    $xml['line']       = $this->getLineNumber();

    return parent::__toXml($xml);
  }
}