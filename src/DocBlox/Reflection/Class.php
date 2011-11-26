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
 * Parses a class definition.
 *
 * @category DocBlox
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Reflection_Class extends DocBlox_Reflection_Interface
{
    /** @var bool Remembers whether this class is abstract */
    protected $abstract = false;

    /** @var bool Remembers whether this class is final */
    protected $final = false;

    /**
     * Retrieves the generic information.
     *
     * Finds out whether this class is abstract and/or final on top of the
     * information found using the DocBlox_Reflection_Interface parent method.
     *
     * @param DocBlox_Reflection_TokenIterator $tokens
     *
     * @see DocBlox_Reflection_Interface::processGenericInformation
     *
     * @return void
     */
    protected function processGenericInformation(
        DocBlox_Reflection_TokenIterator $tokens
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

    /**
     * Returns the XML representation of this object or false if an error occurred.
     *
     * @param SimpleXMLElement $xml If not null, expands the given SimpleXML
     *     Node instead of instantiating a new one.
     *
     * @return string|boolean
     */
    public function __toXml(SimpleXMLElement $xml = null)
    {
        if ($xml === null) {
            $xml = new SimpleXMLElement('<class></class>');
        }

        $xml['final'] = $this->isFinal() ? 'true' : 'false';
        $xml['abstract'] = $this->isAbstract() ? 'true' : 'false';
        $xml['line'] = $this->getLineNumber();

        return parent::__toXml($xml);
    }
}