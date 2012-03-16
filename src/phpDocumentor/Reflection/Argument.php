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
 * Parses a method or function Argument.
 *
 * @category phpDocumentor
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Reflection_Argument extends phpDocumentor_Reflection_Abstract
{
    /**
     * The default value of this argument, or null if none available.
     *
     * @var string|null
     */
    protected $default = null;

    /**
     * The type of this argument.
     *
     * This is a free text field and thus could contain any typehinting value
     * like stdClass or array. If no typehint was provided this variable
     * contains null.
     *
     * @var string|null
     */
    protected $type = null;

    /**
     * Retrieves the generic information.
     *
     * Find the name, type and default value for this argument.
     *
     * @param phpDocumentor_Reflection_TokenIterator $tokens Tokens to interpret with
     *     the pointer at the token to be processed.
     *
     * @return void
     */
    protected function processGenericInformation(
        phpDocumentor_Reflection_TokenIterator $tokens
    ) {
        $this->setName($tokens->current()->content);
        $this->type = $this->findType($tokens);
        $this->default = $this->findDefault($tokens);
    }

    /**
     * Returns the default value or null is none is set.
     *
     * @return null|string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Returns the typehint, or null if none is set.
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the name of this argument.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}