<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Static_Reflection
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Reflection class for a DocBloxk Tag declaration.
 *
 * @category   DocBlox
 * @package    Static_Reflection
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Reflection_DocBlock_Tag implements Reflector
{
  /** @var string Name of the tag */
  protected $tag = '';

  /** @var string content of the tag */
  protected $content = '';

  /** @var string description of the content of this tag */
  protected $description = '';

  /**
   * Factory method responsible for instantiating the correct sub type.
   *
   * @throws DocBlox_Reflection_Exception if an invalid tag line was presented.
   *
   * @return void
   */
  public static function createInstance($tag_line)
  {
    if (!preg_match('/^@([\w\-\_]+)(?:\s+([^\s].*)|$)?/us', $tag_line,
        $matches))
    {
      throw new DocBlox_Reflection_Exception('Invalid tag_line detected: '.$tag_line);
    }
    $class_name = 'DocBlox_Reflection_DocBlock_Tag_'.ucfirst($matches[1]);

    return (@class_exists($class_name))
      ? new $class_name($matches[1], isset($matches[2]) ? $matches[2] : '')
      : new self($matches[1], isset($matches[2]) ? $matches[2] : '');
  }

  /**
   * Parses a tag and populates the member variables.
   *
   * @param string $tag_line Line containing the full tag
   *
   * @return void
   */
  public function __construct($type, $content)
  {
    $this->tag         = $type;
    $this->content     = $content;
    $this->description = $content;
  }

  /**
   * Returns the name of this tag.
   *
   * @return string
   */
  public function getName()
  {
    return $this->tag;
  }

  /**
   * Returns the content of this tag.
   *
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }

  /**
   * Returns the description component of this tag.
   *
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * Builds a string representation of this object.
   *
   * @todo determine the exact format as used by PHP Reflection and implement it.
   *
   * @return void
   */
  static public function export()
  {
    throw new Exception('Not yet implemented');
  }

  /**
   * Returns the exported information (we should use the export static method BUT this throws an
   * exception at this point).
   *
   * @return void
   */
  public function __toString()
  {
    return 'Not yet implemented';
  }
}