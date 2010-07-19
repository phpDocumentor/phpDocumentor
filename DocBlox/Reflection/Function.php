<?php
class DocBlox_Reflection_Function extends DocBlox_Reflection_BracesAbstract
{
  protected $doc_block  = null;
  protected $arguments_token_start = 0;
  protected $arguments_token_end   = 0;

  protected $arguments     = array();

  protected function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    $this->setName($this->findName($tokens));

    $this->resetTimer();
    $this->doc_block  = $this->findDocBlock($tokens);

    list($start_index, $end_index) = $tokens->getTokenIdsOfParenthesisPair();
    $this->arguments_token_start = $start_index;
    $this->arguments_token_end   = $end_index;
    $this->debugTimer('>> Determined argument range token ids');
  }

  public function processVariable(DocBlox_TokenIterator $tokens)
  {
    // is the variable occurs within arguments parenthesis then it is an argument
    if (($tokens->key() > $this->arguments_token_start) && ($tokens->key() < $this->arguments_token_end))
    {
      $this->resetTimer('variable');

      $argument = new DocBlox_Reflection_Argument();
      $argument->parseTokenizer($tokens);
      $this->arguments[$argument->getName()] = $argument;

      $this->debugTimer('>> Processed argument '.$argument->getName(), 'variable');
    }
  }

  protected function findName(DocBlox_TokenIterator $tokens)
  {
    return $tokens->findNextByType(T_STRING, 5, array('{', ';'))->getContent();
  }

  public function getDocBlock()
  {
    return $this->doc_block;
  }

  public function __toXml()
  {
    $xml = new SimpleXMLElement('<function></function>');
    $xml['namespace']  = $this->getNamespace();
    $xml->name = $this->getName();
    $this->addDocblockToSimpleXmlElement($xml);

    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->loadXML($xml->asXML());

    foreach ($this->arguments as $argument)
    {
      $this->mergeXmlToDomDocument($dom, $argument->__toXml());
    }

    return trim($dom->saveXML());
  }

  public function __toString()
  {
    return $this->getName();
  }

}