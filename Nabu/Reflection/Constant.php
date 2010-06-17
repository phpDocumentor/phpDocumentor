<?php
class Nabu_Reflection_Constant extends Nabu_Abstract
{
  protected $name        = '';
  protected $docBlock    = null;
  protected $value       = '';

  public function parseTokenizer(Nabu_TokenIterator $tokens)
  {
    // extract general information
    $this->name       = $tokens->current()->getContent();

    // check for a default value, can be an array, string type (also boolean and null) or integer
    $default_token        = $tokens->findNextByType(T_STRING, 5, array(';'));
    if (!$default_token)
    {
      $default_token      = $tokens->findNextByType(T_LNUMBER, 5, array(';'));
    }
    if (!$default_token)
    {
      $default_token      = $tokens->findNextByType(T_ARRAY, 5, array(';'));
    }
    $this->value    = $default_token ? $default_token->getContent() : null;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getValue()
  {
    return $this->value;
  }

  public function getDocBlock()
  {
    return $this->docBlock;
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

    return $xml->asXML();
  }
}