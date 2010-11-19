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
class DocBlox_Reflection_Variable extends DocBlox_Reflection_DocBlockedAbstract
{
  protected $default     = null;

  protected function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    $this->setName($tokens->current()->getContent());
    $this->default    = $this->findDefault($tokens);

    parent::processGenericInformation($tokens);
  }

  public function getDefault()
  {
    return $this->default;
  }

  public function __toXml(SimpleXMLElement $xml = null)
  {
    if ($xml === null)
    {
      $xml = new SimpleXMLElement('<variable></variable>');
    }

    $xml->name    = $this->getName();
    $xml->default = $this->getDefault();
    $xml['line']  = $this->getLineNumber();

    $this->addDocblockToSimpleXmlElement($xml);

    return $xml->asXML();
  }
}
