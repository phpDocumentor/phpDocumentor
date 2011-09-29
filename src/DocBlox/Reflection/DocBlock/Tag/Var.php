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
 * Reflection class for a @var tag in a Docblock.
 *
 * @category   DocBlox
 * @package    Reflection
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Reflection_DocBlock_Tag_Var extends DocBlox_Reflection_DocBlock_Tag_Param
{
  /**
   * Parses a tag and populates the member variables.
   *
   * @throws DocBlox_Reflection_Exception if an invalid tag line was presented
   *
   * @param string $type    Tag identifier for this tag (should be 'return')
   * @param string $content Contents for this tag.
   */
  public function __construct($type, $content)
  {
    $this->tag = $type;
    $this->content = $content;
    $content = preg_split('/\s+/u', $content);

    if (count($content) == 0) {
      return;
    }

    // var always starts with the variable name
    $this->type = array_shift($content);

    // if the next item starts with a $ it must be the variable name
    if ((count($content) > 0) && (strlen($content[0]) > 0) && ($content[0][0] == '$')) {
      $this->variableName = array_shift($content);
    }

    $this->description = implode(' ', $content);
  }
}
