<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Reflection
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

/**
 * Reflection class for a @return tag in a Docblock.
 *
 * @category phpDocumentor
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Reflection_DocBlock_Tag_Return
    extends phpDocumentor_Reflection_DocBlock_Tag_Param
{
    /** @var string */
    protected $type = null;

    /**
     * Parses a tag and populates the member variables.
     *
     * @param string $type    Tag identifier for this tag (should be 'return')
     * @param string $content Contents for this tag.
     *
     * @throws phpDocumentor_Reflection_Exception if an invalid tag line was presented
     */
    public function __construct($type, $content)
    {
        $this->tag = $type;
        $this->content = $content;
        $content = preg_split('/\s+/u', $content);

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
