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
      // find the first encapsed string and strip the opening and closing
      // apostrophe
      $name_token = $tokens->gotoNextByType(
          T_CONSTANT_ENCAPSED_STRING, 5, array(',')
      );

      if (!$name_token)
      {
          $this->log(
              'Unable to process constant in file ' . $tokens->getFilename()
              . ' at line ' . $tokens->current()->getLineNumber(),
              DocBlox_Core_Log::CRIT
          );
          return;
      }

      $this->setName(substr($name_token->getContent(), 1, -1));

      // skip to after the comma
      while($tokens->current()->getContent() != ',')
      {
        if ($tokens->next() === false)
        {
          break;
        }
      }

      // get everything until the closing brace and use that for value, take child parenthesis under consideration
      $value = '';
      $level = 0;
      while (!(($tokens->current()->getContent() == ')') && ($level == -1)))
      {
        if ($tokens->next() === false)
        {
          break;
        }

        switch($tokens->current()->getContent())
        {
          case '(': $level++; break;
          case ')': $level--; break;
        }

        $value .= $tokens->current()->getContent();
      }

      $this->setValue(trim(substr($value, 0, -1)));
    }
    else
    {
      $this->setName($tokens->gotoNextByType(T_STRING, 5, array('='))->getContent());
      $this->setValue($this->findDefault($tokens));
    }

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
    if (!$this->getName())
    {
      $xml = new SimpleXMLElement('');
      return $xml->asXML();
    }

    $xml = new SimpleXMLElement('<constant></constant>');
    $xml->name         = $this->getName();
    $xml->value        = $this->getValue();
    $xml['namespace']  = $this->getNamespace();
    $xml['line']       = $this->getLineNumber();
    $this->addDocblockToSimpleXmlElement($xml);

    return $xml->asXML();
  }

}