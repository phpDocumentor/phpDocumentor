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
 * Reflection class for a generic variable.
 *
 * @category phpDocumentor
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Reflection_Variable extends phpDocumentor_Reflection_DocBlockedAbstract
{
    /**
     * @var string|null contains the default value or null if none present;
     * please note that it may contain 'null'
     */
    protected $default = null;

    /**
     * Retrieves the generic information.
     *
     * Finds out whether this variable has a default value and sets the name on
     * top of the information found using the
     * phpDocumentor_Reflection_DocBlockedAbstract parent method.
     *
     * @param phpDocumentor_Reflection_TokenIterator $tokens Tokens to interpret with
     *     the pointer at the token to be processed.
     *
     * @see phpDocumentor_Reflection_DocBlockedAbstract::processGenericInformation
     *
     * @return void
     */
    protected function processGenericInformation(
        phpDocumentor_Reflection_TokenIterator $tokens
    ) {
        $this->setName($tokens->current()->content);
        $this->default = $this->findDefault($tokens);

        parent::processGenericInformation($tokens);
    }

    /**
     * Returns the default value or null if none found.
     *
     * Please note that if the default value is null that this method returns
     * string 'null'.
     *
     * @return null|string
     */
    public function getDefault()
    {
        return $this->default;
    }

}
