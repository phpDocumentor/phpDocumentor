<?php
class DocBlox_Reflection_Include extends DocBlox_Reflection_Abstract
{
  protected $type = '';

  public function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    $this->type = ucwords(strtolower(str_replace('_', ' ', substr($tokens->current()->getName(), 2))));
    $this->name = $tokens->gotoNextByType(T_CONSTANT_ENCAPSED_STRING, 10, array(';'))->getContent();
  }

  public function getType()
  {
    return $this->type;
  }

  public function __toXml()
  {
    $xml = new SimpleXMLElement('<include></include>');
    $xml->name         = $this->getName();
    $xml['type']       = $this->getType();
    $xml['namespace']  = $this->getNamespace();

    return $xml->asXML();
  }

  public function __toString()
  {
    return $this->getName();
  }
}