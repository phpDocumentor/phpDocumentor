<?php
class Nabu_File extends Nabu_Reflection_Abstract
{
  protected $filename         = '';
  protected $tokens           = null;
  protected $contents         = '';
  protected $classes          = array();
  protected $functions        = array();
  protected $active_namespace = 'default';

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

    $contents = file_get_contents($file);
    $this->resetTimer('md5');
    $this->hash = md5($contents);
    $this->debugTimer('Hashed contents', 'md5');

    $tokens = token_get_all($contents);
    $this->tokens = new Nabu_TokenIterator($tokens);
  }

  public function process()
  {
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

  protected function processNamespace(Nabu_TokenIterator $tokens)
  {
    // collect all namespace parts
    $namespace = array();
    while($token = $tokens->gotoNextByType(T_STRING, 5, array(';', '{')))
    {
      $namespace[] = $token->getContent();
    }
    $namespace = implode('\\', $namespace);

    $this->active_namespace = $namespace;
  }

  protected function processClass(Nabu_TokenIterator $tokens)
  {
    $class = new Nabu_Reflection_Class();
    $class->setNamespace($this->active_namespace);
    $class->parseTokenizer($tokens);
    $this->log('Found class: '.$class->getName());

    $this->classes[$class->getName()] = $class;
  }

  protected function processFunction(Nabu_TokenIterator $tokens)
  {
    $function = new Nabu_Reflection_Function();
    $function->setNamespace($this->active_namespace);
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
