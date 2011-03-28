<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Static_Reflection
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Reflection class for a @return tag in a Docblock.
 *
 * @category   DocBlox
 * @package    Static_Reflection
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Reflection_DocBlock_Tag_Return extends DocBlox_Reflection_DocBlock_Tag
{
  /** @var string */
  protected $type = null;

  /**
   * Parses a tag and populates the member variables.
   *
   * @throws DocBlox_Reflection_Exception if an invalid tag line was presented
   *
   * @param string $tag_line Line containing the full tag
   *
   * @return void
   */
  public function __construct($type, $content)
  {
    $this->tag     = $type;
    $this->content = $content;
    $content   = preg_split('/\s+/u', $content);

    // any output is considered a type
    $this->type = array_shift($content);

    $this->description = implode(' ', $content);
  }

  /**
   * Returns the type of the variable.
   *
   * @return string
   */
  public function getTypes()
  {
    $types = explode('|', $this->type);
    array_walk($types, 'trim');
    return $types;
  }

  /**
   * Returns the type of the variable.
   *
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}
