<?php
class DocBlox_Reflection_Constant extends DocBlox_Reflection_DocBlockedAbstract
{
  protected $value     = '';

  protected function processGenericInformation(DocBlox_Token_Iterator $tokens)
  {
    $this->setName($tokens->gotoNextByType(T_STRING, 5, array('='))->getContent());
    $this->setValue($this->findDefault($tokens));

    parent::processGenericInformation($tokens);
  }

  public function setValue($value)
  {
    $this->value = $value;
  }

  public function getValue()
  {
    return $this->value;
  }

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