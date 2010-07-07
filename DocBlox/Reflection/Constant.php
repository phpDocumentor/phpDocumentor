<?php
class DocBlox_Reflection_Constant extends DocBlox_Reflection_Abstract
{
  protected $doc_block = null;
  protected $value     = '';

  public function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    $this->name      = $tokens->gotoNextByType(T_STRING, 5, array('='))->getContent();

    $this->value     = $this->findDefault($tokens);
    $this->doc_block = $this->findDocBlock($tokens);
  }

  public function getValue()
  {
    return $this->value;
  }

  public function getDocBlock()
  {
    return $this->doc_block;
  }

  public function __toString()
  {
    return $this->getName();
  }

  public function __toXml()
  {
    $xml = new SimpleXMLElement('<constant></constant>');
    $xml->name         = $this->getName();
    $xml->value        = $this->getValue();
    $xml['namespace']  = $this->getNamespace();
    $this->addDocblockToSimpleXmlElement($xml);

    return $xml->asXML();
  }
}