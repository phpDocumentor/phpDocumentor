<?php
class Nabu_Reflection_Method extends Nabu_Reflection_Function
{
  protected $abstract   = false;
  protected $final      = false;
  protected $static     = false;
  protected $visibility = 'public';

  protected function processGenericInformation(Nabu_TokenIterator $tokens)
  {
    $this->static     = $this->findStatic($tokens)   ? true : false;
    $this->abstract   = $this->findAbstract($tokens) ? true : false;
    $this->final      = $this->findFinal($tokens)    ? true : false;
    $this->visibility = $this->findVisibility($tokens);

    parent::processGenericInformation($tokens);
  }

  public function getVisibility()
  {
    return $this->visibility;
  }

  public function isAbstract()
  {
    return $this->abstract;
  }

  public function isStatic()
  {
    return $this->static;
  }

  public function isFinal()
  {
    return $this->final;
  }

  public function __toXml()
  {
    $xml = new SimpleXMLElement('<method></method>');
    $xml->name         = $this->getName();
    $xml['final']      = $this->isFinal() ? 'true' : 'false';
    $xml['abstract']   = $this->isAbstract() ? 'true' : 'false';
    $xml['static']   = $this->isStatic() ? 'true' : 'false';
    $xml['visibility'] = $this->getVisibility();

    $this->addDocblockToSimpleXmlElement($xml);

    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->loadXML($xml->asXML());

    // import methods into class xml
    foreach ($this->arguments as $argument)
    {
      $this->mergeXmlToDomDocument($dom, $argument->__toXml());
    }

    return trim($dom->saveXML());
  }
}