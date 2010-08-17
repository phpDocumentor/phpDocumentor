<?php
class DocBlox_Reflection_Class extends DocBlox_Reflection_Interface
{
  protected $abstract    = false;
  protected $final       = false;

  protected function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    // retrieve generic information about the class
    $this->abstract  = $this->findAbstract($tokens) ? true : false;
    $this->final     = $this->findFinal($tokens)    ? true : false;

    parent::processGenericInformation($tokens);
  }

  public function isAbstract()
  {
    return $this->abstract;
  }

  public function isFinal()
  {
    return $this->final;
  }

  public function __toXml(SimpleXMLElement $xml = null)
  {
    $xml = new SimpleXMLElement('<class></class>');
    $xml['final']      = $this->isFinal() ? 'true' : 'false';
    $xml['abstract']   = $this->isAbstract() ? 'true' : 'false';

    return parent::__toXml($xml);
  }
}