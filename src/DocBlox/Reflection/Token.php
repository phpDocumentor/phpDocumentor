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
class DocBlox_Reflection_Token
{
  /** @var int|null Type of the Token; either on of the T_* constants of null in case of a literal */
  public $type = null;

  /** @var string The full content of the token */
  public $content = '';

  /** @var int Line number where the token resides */
  public $line_number = 0;

  /**
   * Instantiate a token and populate it.
   *
   * @param string|mixed[] $content The string content of the token or the 3 element notation used by the Tokenizer/ext
   * @param int|null       $type
   * @param int            $line
   */
  public function __construct($content, $type = null, $line = 0)
  {
    // index 2 only exists in case of an array; this is faster than is_array()
    if (isset($content[2]))
    {
      $temp_content = $content;
      list($type, $content, $line) = $temp_content;
    }

    $this->type        = $type;
    $this->content     = $content;
    $this->line_number = $line;
  }

  /**
   * Returns the name for this type of token; or null in case of a literal.
   *
   * @return null|string
   */
  public function getName()
  {
    $type = $this->type;
    return $type !== null ? token_name($type) : null;
  }

  /**
   * Returns the line number for this token.
   *
   * @return int
   */
  public function getLineNumber()
  {
    return $this->line_number;
  }
}