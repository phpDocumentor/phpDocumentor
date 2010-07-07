<?php
class DocBlox_Reflection_Property extends DocBlox_Reflection_Abstract
{
  protected $doc_block   = null;
  protected $static      = false;
  protected $final       = false;
  protected $default     = null;
  protected $visibility  = 'public';

  protected function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    $this->setName($tokens->current()->getContent());
    $this->static     = $this->findStatic($tokens) ? true : false;
    $this->final      = $this->findFinal($tokens)  ? true : false;
    $this->visibility = $this->findVisibility($tokens);
    $this->doc_block  = $this->findDocBlock($tokens);
    $this->default    = $this->findDefault($tokens);
  }

  public function getDefault()
  {
    return $this->default;
  }

  public function isStatic()
  {
    return $this->static;
  }

  public function isFinal()
  {
    return $this->final;
  }

  public function getVisibility()
  {
    return $this->visibility;
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
    $xml = new SimpleXMLElement('<property></property>');
    $xml->name         = $this->getName();
    $xml->default      = $this->getDefault();
    $xml['final']      = $this->isFinal() ? 'true' : 'false';
    $xml['static']     = $this->isStatic() ? 'true' : 'false';
    $xml['visibility'] = $this->getVisibility();;

    $this->addDocblockToSimpleXmlElement($xml);

    return $xml->asXML();
  }
}