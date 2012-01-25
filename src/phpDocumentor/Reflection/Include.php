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
 * Parses an include definition.
 *
 * @category phpDocumentor
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Reflection_Include extends phpDocumentor_Reflection_DocBlockedAbstract
{
    /**
     * @var string Which type of include is this? Include, Include Once,
     *     Require or Require Once?
     */
    protected $type = '';

    /**
     * Get the type and name for this include.
     *
     * @param phpDocumentor_Reflection_TokenIterator $tokens Tokens to interpret with
     *     the pointer at the token to be processed.
     *
     * @return void
     */
    protected function processGenericInformation(
        phpDocumentor_Reflection_TokenIterator $tokens
    ) {
        parent::processGenericInformation($tokens);

        $this->type = ucwords(
            strtolower(
                str_replace('_', ' ', substr($tokens->current()->getName(), 2))
            )
        );

        $token = $tokens->gotoNextByType(
            T_CONSTANT_ENCAPSED_STRING, 10, array(';')
        );

        if ($token) {
            $this->setName(trim($token->content, '\'"'));
        } elseif ($token = $tokens->gotoNextByType(T_VARIABLE, 10, array(';'))) {
            $this->setName(trim($token->content, '\'"'));
        }
    }

    /**
     * Returns the type of this include.
     *
     * Valid types are:
     * - Include
     * - Include Once
     * - Require
     * - Require Once
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the name for this object.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}