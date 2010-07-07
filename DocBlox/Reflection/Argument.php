<?php
class DocBlox_Reflection_Argument extends DocBlox_Reflection_Abstract
{
  protected $name        = '';
  protected $default     = null;
  protected $type        = null;

  public function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    $this->name    = $tokens->current()->getContent();
    $this->type    = $this->findType($tokens);
    $this->default = $this->findDefault($tokens);
  }

  public function getName()
  {
    return $this->name;
  }

  public function getDefault()
  {
    return $this->default;
  }

  public function getType()
  {
    return $this->type;
  }

  public function __toString()
  {
    return $this->getName();
  }

  public function __toXml()
  {
    $xml = new SimpleXMLElement('<argument></argument>');
    $xml->name         = $this->getName();
    $xml->default      = $this->getDefault();
    $xml->type         = $this->getType();

    return $xml->asXML();
  }
}