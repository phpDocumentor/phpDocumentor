<?php
class Nabu_File extends Nabu_Abstract
{
  protected $filename   = '';
  protected $tokens     = null;
  protected $contents   = '';
  protected $classes    = array();
  protected $functions    = array();

  public function __construct($file)
  {
    parent::__construct();

    if (!is_string($file) || (!is_readable($file)))
    {
      throw new Nabu_Reflection_Exception('The given file should be a string, should exist on the filesystem and should be readable');
    }

    exec('php -l '.escapeshellarg($file), $output, $result);
    if ($result != 0)
    {
      throw new Nabu_Reflection_Exception('The given file could not be interpreted as it contains errors: '.implode(PHP_EOL, $output));
    }

    $this->filename = $file;
    $tokens = token_get_all(file_get_contents($file));
    $this->tokens = new Nabu_TokenIterator($tokens);
    $this->parseTokenizer($this->tokens);

    // preserve memory by unsetting the $this->tokens
    unset($this->tokens);
  }

  public function processGenericInformation(Nabu_TokenIterator $tokens)
  {
    // find file docblock; standard function does not suffice as this scans backwards and we have to make sure it isn't
    // the docblock of another element
  }

  public function processTokens(Nabu_TokenIterator $tokens)
  {
    $token = null;
    while ($tokens->valid())
    {
      $token = $token === null ? $tokens->current() : $tokens->next();

      if ($token && $token->getType())
      {
        $this->processToken($token, $tokens);
      }
    }
  }

  protected function processClass(Nabu_TokenIterator $tokens)
  {
    $class = new Nabu_Reflection_Class();
    $class->parseTokenizer($tokens);
    $this->log('Found class: '.$class->getName());

    $this->classes[$class->getName()] = $class;
  }

  protected function processFunction(Nabu_TokenIterator $tokens)
  {
    $function = new Nabu_Reflection_Function();
    $function->parseTokenizer($tokens);
    $this->log('Found function: '.$function->getName());

    $this->functions[$function->getName()] = $function;
  }

  public function __toXml()
  {
    $xml_text  = '<?xml version="1.0" encoding="utf-8"?>';
    $xml_text .= '<file path="'.$this->filename.'">';
    foreach($this->functions as $function)
    {
      $function = explode("\n", trim($function->__toXml()));
      $xml_text .= array_pop($function);
    }
    foreach($this->classes as $class)
    {
      $class = explode("\n", trim($class->__toXml()));
      $xml_text .= array_pop($class);
    }
    $xml_text .= '</file>';
    return $xml_text;
  }
}
