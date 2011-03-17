<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Tokens
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Iterator class responsible for navigating through a list of Tokens.
 *
 * @category DocBlox
 * @package  Tokens
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Token
{
  /** @var int|null Type of the Token; either on of the T_* constants of null in case of a literal */
  protected $type = null;

  /** @var string The full content of the token */
  protected $content = '';

  /** @var int Line number where the token resides */
  protected $line = 0;

  /**
   * Instantiate a token and populate it.
   *
   * @param string|mixed[] $content The string content of the token or the 3 element notation used by the Tokenizer/ext
   * @param int|null       $type
   * @param int            $line
   */
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

  /**
   * Returns the name for this type of token; or null in case of a literal.
   *
   * @return null|string
   */
  public function getName()
  {
    return $this->getType() !== null ? token_name($this->getType()) : null;
  }

  /**
   * Returns the contents of this token; in case of a literal, which one.
   *
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }

  /**
   * Returns the type identifier for this token, matched the T_* sequence.
   *
   * @return int|null
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * Returns the line number for this token.
   *
   * @return int
   */
  public function getLineNumber()
  {
    return $this->line;
  }
}