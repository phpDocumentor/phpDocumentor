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
class DocBlox_Reflection_Variable extends DocBlox_Reflection_Abstract
{
  protected $doc_block   = null;
  protected $default     = null;

  protected function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    $this->setName($tokens->current()->getContent());
    $this->doc_block  = $this->findDocBlock($tokens);
    $this->default    = $this->findDefault($tokens);
  }

  public function getDefault()
  {
    return $this->default;
  }

  public function getDocBlock()
  {
    return $this->doc_block;
  }

  public function __toString()
  {
    return $this->getName();
  }

  public function __toXml(SimpleXMLElement $xml = null)
  {
    if ($xml === null)
    {
      $xml = new SimpleXMLElement('<variable></variable>');
    }

    $xml->name    = $this->getName();
    $xml->default = $this->getDefault();
    $this->addDocblockToSimpleXmlElement($xml);

    return $xml->asXML();
  }
}
