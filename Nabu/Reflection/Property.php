<?php
class Nabu_Reflection_Property extends Nabu_Abstract
{
  protected $name        = '';
  protected $docBlock    = null;
  protected $static      = false;
  protected $final       = false;
  protected $default     = null;
  protected $visibility  = 'public';

  public function parseTokenizer(Nabu_TokenIterator $tokens)
  {
    // extract general information
    $this->name   = $tokens->current()->getContent();
    $this->static = $tokens->findPreviousByType(T_STATIC, 5, array('{', ';')) ? true : false;
    $this->final  = $tokens->findPreviousByType(T_FINAL, 5, array('{', ';')) ? true : false;

    // determine visibility
    $this->visibility = 'public';
    $this->visibility = $tokens->findPreviousByType(T_PRIVATE, 5, array('{', ';')) ? 'private' : $this->visibility;
    $this->visibility = $tokens->findPreviousByType(T_PROTECTED, 5, array('{', ';')) ? 'protected' : $this->visibility;

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
    return $this->docBlock;
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

    return $xml->asXML();
  }
}