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
 * Parses a class definition.
 *
 * @category phpDocumentor
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Reflection_Class extends phpDocumentor_Reflection_Interface
{
    /** @var bool Remembers whether this class is abstract */
    protected $abstract = false;

    /** @var bool Remembers whether this class is final */
    protected $final = false;

    /**
     * Retrieves the generic information.
     *
     * Finds out whether this class is abstract and/or final on top of the
     * information found using the phpDocumentor_Reflection_Interface parent method.
     *
     * @param phpDocumentor_Reflection_TokenIterator $tokens Tokens to interpret with
     *     the pointer at the token to be processed.
     *
     * @see phpDocumentor_Reflection_Interface::processGenericInformation
     *
     * @return void
     */
    protected function processGenericInformation(
        phpDocumentor_Reflection_TokenIterator $tokens
    ) {
        // retrieve generic information about the class
        $this->abstract = $this->findAbstract($tokens) ? true : false;
        $this->final = $this->findFinal($tokens) ? true : false;

        parent::processGenericInformation($tokens);
    }

    /**
     * Returns whether this class definition is abstract.
     *
     * @return bool
     */
    public function isAbstract()
    {
        return $this->abstract;
    }

    /**
     * Returns whether this class definition is final.
     *
     * @return bool
     */
    public function isFinal()
    {
        return $this->final;
    }
}