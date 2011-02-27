<?php
class DocBlox_Reflection_Include extends DocBlox_Reflection_Abstract
{
  protected $type = '';

  protected function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    $this->type = ucwords(strtolower(str_replace('_', ' ', substr($tokens->current()->getName(), 2))));

    if ($token = $tokens->gotoNextByType(T_CONSTANT_ENCAPSED_STRING, 10, array(';')))
    {
      $this->setName(trim($token->getContent(), '\'"'));
    }
    elseif ($token = $tokens->gotoNextByType(T_VARIABLE, 10, array(';')))
    {
      $this->setName(trim($token->getContent(), '\'"'));
    }
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
    $xml['line']       = $this->getLineNumber();

    return $xml->asXML();
  }

  public function __toString()
  {
    return $this->getName();
  }
}