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
 * Reflection class for a the property in a class.
 *
 * @category phpDocumentor
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Reflection_Property extends phpDocumentor_Reflection_Variable
{
    /** @var bool Remembers whether this property is static */
    protected $static = false;

    /** @var bool Remembers whether this property is final */
    protected $final = false;

    /**
     * @var string Remember the visibility of this property; may be either
     * public, protected or private
     */
    protected $visibility = 'public';

    /**
     * Retrieves the generic information.
     *
     * Finds out whether this property is static, final and what visibility it
     * has on top of the information found using the phpDocumentor_Reflection_Variable
     * parent method.
     *
     * @param phpDocumentor_Reflection_TokenIterator $tokens Tokens to interpret with
     *     the pointer at the token to be processed.
     *
     * @see phpDocumentor_Reflection_Variable::processGenericInformation
     *
     * @return void
     */
    protected function processGenericInformation(
        phpDocumentor_Reflection_TokenIterator $tokens
    ) {
        $this->static = $this->findStatic($tokens) ? true : false;
        $this->final = $this->findFinal($tokens) ? true : false;
        $this->visibility = $this->findVisibility($tokens);

        parent::processGenericInformation($tokens);
    }

    /**
     * Returns whether this property is static.
     *
     * @return bool
     */
    public function isStatic()
    {
        return $this->static;
    }

    /**
     * Returns whether this property is final.
     *
     * @return bool
     */
    public function isFinal()
    {
        return $this->final;
    }

    /**
     * Returns the visibility for this item.
     *
     * The returned value should match either of the following:
     *
     * * public
     * * protected
     * * private
     *
     * If a property has no visibility set in the class definition this method
     * will return 'public'.
     *
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }
}