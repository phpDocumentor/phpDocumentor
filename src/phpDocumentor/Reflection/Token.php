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
 * Iterator class responsible for navigating through a list of Tokens.
 *
 * This class uses practices that are not considered best practice because they
 * are faster. Many of those can be attributed to 'Micro-optimalization' but
 * this class is invoked thus many times that this matters a great deal.
 *
 * @category phpDocumentor
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Reflection_Token
{
    /**
     * @var int|null Type of the Token; either on of the T_* constants of null
     * in case of a literal
     */
    public $type = null;

    /** @var string The full content of the token */
    public $content = '';

    /** @var int Line number where the token resides */
    public $line_number = 0;

    /**
     * Instantiate a token and populate it.
     *
     * @param string|mixed[] $content The string content of the token or the
     *     3 element notation used by the Tokenizer/ext
     * @param int|null       $type    If not literal, the type id as defined by
     *     the T_* constants.
     * @param int            $line    Line number where the token occurs.
     */
    public function __construct($content, $type = null, $line = 0)
    {
        // index 2 only exists in case of an array; this is faster than is_array()
        if (isset($content[2])) {
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
     * @return string|null
     */
    public function getName()
    {
        if ($this->type !== null) {
            return token_name($this->type);
        }

        return null;
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