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
 * Reflection class for a generic variable.
 *
 * @category DocBlox
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Reflection_Variable extends DocBlox_Reflection_DocBlockedAbstract
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
     * DocBlox_Reflection_DocBlockedAbstract parent method.
     *
     * @param DocBlox_Reflection_TokenIterator $tokens Tokens to interpret with
     *     the pointer at the token to be processed.
     *
     * @see DocBlox_Reflection_DocBlockedAbstract::processGenericInformation
     *
     * @return void
     */
    protected function processGenericInformation(
        DocBlox_Reflection_TokenIterator $tokens
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
