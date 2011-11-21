<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Reflection
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Reflection class for a @see tag in a Docblock.
 *
 * @category DocBlox
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Reflection_DocBlock_Tag_See
    extends DocBlox_Reflection_DocBlock_Tag
{
    /** @var string */
    protected $refers = null;

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

        // any output is considered a type
        $this->refers = array_shift($content);

        $this->description = implode(' ', $content);
    }

    /**
     * Returns the type of the variable.
     *
     * @return string
     */
    public function getReference()
    {
        return $this->refers;
    }

}
