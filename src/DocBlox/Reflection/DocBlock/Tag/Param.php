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
 * Reflection class for a @param tag in a Docblock.
 *
 * @category DocBlox
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Reflection_DocBlock_Tag_Param extends DocBlox_Reflection_DocBlock_Tag
{
    /** @var string */
    protected $type = null;

    /**
     * @var string
     */
    protected $variableName = null;

    /**
     * Parses a tag and populates the member variables.
     *
     * @param string $type    Tag identifier for this tag (should be 'return')
     * @param string $content Contents for this tag.
     *
     * @throws DocBlox_Reflection_Exception if an invalid tag line was presented
     */
    public function __construct($type, $content)
    {
        $this->tag = $type;
        $this->content = $content;
        $content = preg_split('/\s+/u', $content);

        // if there is only 1, it is either a piece of content or a variable name
        if (count($content) > 1) {
            $this->type = array_shift($content);
        }

        // if the next item starts with a $ it must be the variable name
        if ((strlen($content[0]) > 0) && ($content[0][0] == '$')) {
            $this->variableName = array_shift($content);
        }

        $this->description = implode(' ', $content);
    }

    /**
     * Returns the unique types of the variable.
     *
     * @return string[]
     */
    public function getTypes()
    {
        $types = explode('|', $this->type);
        array_walk($types, 'trim');
        return $types;
    }

    /**
     * Returns the type section of the variable.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the variable's name.
     *
     * @return string
     */
    public function getVariableName()
    {
        return $this->variableName;
    }

    /**
     * Sets the variable's name.
     *
     * @param string $name The new name for this variable.
     * @return void
     */
    public function setVariableName($name)
    {
        $this->variableName = $name;
    }
}
