<?php
class Nabu_Reflection_Argument extends Nabu_Abstract
{
  protected $name        = '';
  protected $default     = null;
  protected $type        = null;
  protected $class       = null;
  protected $token_start = 0;
  protected $token_end   = 0;

  public function parseTokenizer(Nabu_TokenIterator $tokens)
  {
    // extract general information
    $this->name       = $tokens->current()->getContent();
    $type = $tokens->findPreviousByType(T_STRING, 5, array(',', '('));
    if (!$type)
    {
      $type = $tokens->findPreviousByType(T_ARRAY, 5, array(',', '('));
    }
    $this->type = $type ? $type->getContent() : null;

    // check for a default value, can be an array, string type (also boolean and null) or integer
    $default_token        = $tokens->findNextByType(T_STRING, 5, array(',', ')'));
    if (!$default_token)
    {
      $default_token      = $tokens->findNextByType(T_LNUMBER, 5, array(',', ')'));
    }
    if (!$default_token)
    {
      $default_token      = $tokens->findNextByType(T_ARRAY, 5, array(',', ')'));
    }
    $this->default    = $default_token ? $default_token->getContent() : null;
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

  public function getStartTokenId()
  {
    return $this->token_start;
  }

  public function getEndTokenId()
  {
    return $this->token_end;
  }

  public function setClass(Nabu_Reflection_Class $class)
  {
    $this->class = $class;
  }

  public function getClass()
  {
    return $this->class;
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