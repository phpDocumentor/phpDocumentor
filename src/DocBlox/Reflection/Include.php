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
 * Parses an include definition.
 *
 * @category   DocBlox
 * @package    Reflection
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Reflection_Include extends DocBlox_Reflection_Abstract
{
  /** @var string Which type of include is this? Include, Include Once, Require or Require Once? */
  protected $type = '';

  /**
   * Get the type and name for this include.
   *
   * @param DocBlox_Reflection_TokenIterator $tokens
   *
   * @return void
   */
  protected function processGenericInformation(DocBlox_Reflection_TokenIterator $tokens)
  {
    $this->type = ucwords(strtolower(str_replace('_', ' ', substr($tokens->current()->getName(), 2))));

    if ($token = $tokens->gotoNextByType(T_CONSTANT_ENCAPSED_STRING, 10, array(';')))
    {
      $this->setName(trim($token->content, '\'"'));
    }
    elseif ($token = $tokens->gotoNextByType(T_VARIABLE, 10, array(';')))
    {
      $this->setName(trim($token->content, '\'"'));
    }
  }

  /**
   * Returns the type of this include.
   *
   * Valid types are:
   * - Include
   * - Include Once
   * - Require
   * - Require Once
   *
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * Generates a DocBlox compatible XML output for this object.
   *
   * @return string
   */
  public function __toXml()
  {
    $xml = new SimpleXMLElement('<include></include>');
    $xml->name         = $this->getName();
    $xml['type']       = $this->getType();
    $xml['line']       = $this->getLineNumber();

    return $xml->asXML();
  }

  /**
   * Returns the name for this object.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->getName();
  }
}