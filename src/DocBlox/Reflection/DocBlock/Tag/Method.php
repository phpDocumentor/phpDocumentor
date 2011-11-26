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
 * Reflection class for a @method tag in a Docblock.
 *
 * @category DocBlox
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Reflection_DocBlock_Tag_Method
    extends DocBlox_Reflection_DocBlock_Tag_Param
{

    protected $method_name = '';

    protected $arguments = '';

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

        $matches = array();
        // 1. none or more whitespace
        // 2. optionally a word with underscores followed by whitespace : as
        //    type for the return value
        // 3. then optionally a word with underscores followed by () and
        //    whitespace : as method name as used by phpDocumentor
        // 4. then a word with underscores, followed by ( and any character
        //    until a ) and whitespace : as method name with signature
        // 5. any remaining text : as description
        if (preg_match(
            '/^[\s]*(?:([\w_]+)[\s]+)?(?:[\w_]+\(\)[\s]+)?([\w_]+)\(([^\)]*)\)'
            .'[\s]+(.*)/u',
            $content,
            $matches
        )) {
            list(
                ,
                $this->type,
                $this->method_name,
                $this->arguments,
                $this->description
            ) = $matches;
        } else {
            echo date('c') . ' ERR (3): @method contained invalid contents: '
                . $this->content . PHP_EOL;
        }
    }

    public function setMethodName($method_name)
    {
        $this->method_name = $method_name;
    }

    public function getMethodName()
    {
        return $this->method_name;
    }

    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    public function getArguments()
    {
        if (empty($this->arguments)) {
            return array();
        }

        $arguments = explode(',', $this->arguments);
        foreach ($arguments as $key => $value) {
            $arguments[$key] = explode(' ', trim($value));
        }

        return $arguments;
    }

}
