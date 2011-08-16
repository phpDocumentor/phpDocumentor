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
 * Reflection class for a mistyped @throws tag called @throw in a Docblock.
 *
 * This is a very common error, so @throw is aliased to be @throws
 *
 * @category   DocBlox
 * @package    Reflection
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Reflection_DocBlock_Tag_Throw extends DocBlox_Reflection_DocBlock_Tag_Throws
{
  /**
   * Sets the type to @throws and lets parent parse the tag and populates the member variables.
   *
   * @throws DocBlox_Reflection_Exception if an invalid tag line was presented
   *
   * @param string $type    Tag identifier for this tag (should be 'return')
   * @param string $content Contents for this tag.
   */
  public function __construct($type, $content)
  {
    if (! 'throw' === $type) {
      throw new Exception("Internal error, ".__CLASS__." was called with $type");
    }
    parent::__construct('throws', $content);
  }
}