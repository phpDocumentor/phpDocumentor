<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Static_Reflection
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Reflection class for a constant.
 *
 * @category   DocBlox
 * @package    Static_Reflection
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Reflection_Constant extends DocBlox_Reflection_DocBlockedAbstract
{
  /** @var string Contains the value contained in the constant */
  protected $value = '';

  /**
   * Retrieves the generic information.
   *
   * Finds out what the name and value is of this constant on top of the information found using the
   * DocBlox_Reflection_DocBlockedAbstract parent method.
   *
   * @param DocBlox_Token_Iterator $tokens
   *
   * @see DocBlox_Reflection_DocBlockedAbstract::processGenericInformation
   *
   * @return void
   */
  protected function processGenericInformation(DocBlox_Token_Iterator $tokens)
  {
    if ($tokens->current()->getContent() == 'define')
    {
      // find the first encapsed string and strip the opening and closing apostrophe
      $this->setName(substr($tokens->gotoNextByType(T_CONSTANT_ENCAPSED_STRING, 5, array(','))->getContent(), 1, -1));
    }
    else
    {
      $this->setName($tokens->gotoNextByType(T_STRING, 5, array('='))->getContent());
    }

    $this->setValue($this->findDefault($tokens));
    parent::processGenericInformation($tokens);
  }

  /**
   * Stores the value contained in this constant.
   *
   * @param string $value
   *
   * @return void
   */
  public function setValue($value)
  {
    $this->value = $value;
  }

  /**
   * Returns the value contained in this Constant.
   *
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * Returns the XML representation of this object or false if an error occurred.
   *
   * @return string|boolean
   */
  public function __toXml()
  {
    $xml = new SimpleXMLElement('<constant></constant>');
    $xml->name         = $this->getName();
    $xml->value        = $this->getValue();
    $xml['namespace']  = $this->getNamespace();
    $xml['line'] = $this->getLineNumber();
    $this->addDocblockToSimpleXmlElement($xml);

    return $xml->asXML();
  }

}