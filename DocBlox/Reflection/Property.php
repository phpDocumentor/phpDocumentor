<?php
class DocBlox_Reflection_Property extends DocBlox_Reflection_Variable
{
  protected $static      = false;
  protected $final       = false;
  protected $visibility  = 'public';

  protected function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    $this->static     = $this->findStatic($tokens) ? true : false;
    $this->final      = $this->findFinal($tokens)  ? true : false;
    $this->visibility = $this->findVisibility($tokens);

    parent::processGenericInformation($tokens);
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

  public function __toXml(SimpleXMLElement $xml = null)
  {
    if ($xml === null)
    {
      $xml = new SimpleXMLElement('<property></property>');
    }

    $xml['final']      = $this->isFinal() ? 'true' : 'false';
    $xml['static']     = $this->isStatic() ? 'true' : 'false';
    $xml['visibility'] = $this->getVisibility();

    return parent::__toXml($xml);
  }
}