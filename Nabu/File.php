<?php
class Nabu_File extends Nabu_Abstract
{
  protected $filename   = '';
  protected $tokens     = null;
  protected $contents   = '';
  protected $namespaces = array();
  protected $classes    = array();

  public function __construct($file)
  {
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

    $this->reflect();

    // preserve memory by unsetting the $this->tokens
    unset($this->tokens);
  }

  protected function reflect()
  {
    $tokens = $this->tokens;

    while ($tokens->valid())
    {
      $token = $tokens->current();
      switch ($token->getType())
      {
        case T_CLASS:
          $class = new Nabu_Reflection_Class();
          $class->parseTokenizer($tokens);
          $this->log('Found class: '.$class->getName());

          $this->classes[$class->getName()] = $class;
          break;
        case '':
          $this->log('Literal encountered ('.$tokens->key().'): '.$token->getContent());;
          break;
        default:
          $this->log('Unhandled token encountered ('.$tokens->key().'): '.$token->getName().(($token->getType() == T_STRING) ? ': '.$token->getContent() : ''), Zend_Log::WARN);
      }

      $tokens->next();
    }
  }

  public function __toXml()
  {
    $xml_text  = '<?xml version="1.0" encoding="utf-8"?>';
    $xml_text .= '<file path="'.realpath($this->filename).'">';
    foreach($this->classes as $class)
    {
      $class = explode("\n", trim($class->__toXml()));
      $xml_text .= array_pop($class);
    }
    $xml_text .= '</file>';
    return $xml_text;
  }
}
