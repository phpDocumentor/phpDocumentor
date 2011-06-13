<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Reflection
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Parses a Long Description of a DocBlock.
 *
 * @category   DocBlox
 * @package    Reflection
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Reflection_DocBlock_LongDescription implements Reflector
{
  /** @var string */
  protected $contents = '';

  /** @var DocBlox_Reflection_DocBlock_Tags[] */
  protected $tags = array();

  /**
   * Parses the string for inline tags and if the Markdown class is included; format the found text.
   *
   * @param string $content
   */
  public function __construct($content)
  {
    if (preg_match('/\{\@(.+?)\}/', $content, $matches))
    {
      array_shift($matches);
      foreach($matches as $tag)
      {
        $this->tags[] = DocBlox_Reflection_DocBlock_Tag::createInstance('@'.$tag);
      }
    }

    $this->contents = trim($content);
  }

  /**
   * Returns the text of this description.
   *
   * @return string
   */
  public function getContents()
  {
    return $this->contents;
  }

  /**
   * Return a formatted variant of the Long Description using MarkDown.
   *
   * @todo this should become a more intelligent piece of code where the configuration contains a setting what format
   * long descriptions are.
   *
   * @return string
   */
  public function getFormattedContents()
  {
    $result = $this->contents;
    if (function_exists('Markdown'))
    {
      $result = Markdown($this->contents);
    }

    return $result;
  }

  /**
   * Returns a list of tags mentioned in the text.
   *
   * @return DocBlox_Reflection_DocBlock_Tags[]
   */
  public function getTags()
  {
    return $this->tags;
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