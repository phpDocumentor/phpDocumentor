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
 * Parses a method definition.
 *
 * @category phpDocumentor
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Reflection_Method extends phpDocumentor_Reflection_Function
{
    /** @var bool Remembers whether this method is abstract */
    protected $abstract = false;

    /** @var bool Remembers whether this method is final */
    protected $final = false;

    /** @var bool Remembers whether this method is static */
    protected $static = false;

    /**
     * @var string Remember the visibility of this method; may be either
     *     public, protected or private
     */
    protected $visibility = 'public';

    /**
     * Retrieves the generic information.
     *
     * Finds out whether this method is abstract, static, final and what
     * visibility it has on top of the information found using the
     * phpDocumentor_Reflection_Function parent method.
     *
     * @param phpDocumentor_Reflection_TokenIterator $tokens Tokens to interpret with
     *     the pointer at the token to be processed.
     *
     * @see phpDocumentor_Reflection_Function::processGenericInformation
     *
     * @return void
     */
    protected function processGenericInformation(
        phpDocumentor_Reflection_TokenIterator $tokens
    ) {
        $this->static = $this->findStatic($tokens) ? true : false;
        $this->abstract = $this->findAbstract($tokens) ? true : false;
        $this->final = $this->findFinal($tokens) ? true : false;
        $this->visibility = $this->findVisibility($tokens);

        parent::processGenericInformation($tokens);
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
     * If a method has no visibility set in the class definition this method
     * will return 'public'.
     *
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Returns whether this method is static.
     *
     * @return bool
     */
    public function isAbstract()
    {
        return $this->abstract;
    }

    /**
     * Returns whether this method is static.
     *
     * @return bool
     */
    public function isStatic()
    {
        return $this->static;
    }

    /**
     * Returns whether this method is final.
     *
     * @return bool
     */
    public function isFinal()
    {
        return $this->final;
    }
}