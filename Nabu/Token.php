<?php
class Nabu_Token
{
  protected $type = null;
  protected $content = '';
  protected $line = 0;

  public function __construct($content, $type = null, $line = 0)
  {
    // if we are dealing with an array it probably comes directly from the token_get_all method
    if (is_array($content))
    {
      $temp_content = $content;
      list($type, $content, $line) = $temp_content;
    }

    $this->type    = $type;
    $this->content = $content;
    $this->line    = $line;
  }

  public function getName()
  {
    return $this->getType() !== null ? token_name($this->getType()) : null;
  }

  public function getContent()
  {
    return $this->content;
  }

  public function getType()
  {
    return $this->type;
  }

  public function getLineNumber()
  {
    return $this->line;
  }
}