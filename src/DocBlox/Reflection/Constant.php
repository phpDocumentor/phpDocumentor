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
 * Parses a constant definition.
 *
 * @category DocBlox
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Reflection_Constant extends DocBlox_Reflection_DocBlockedAbstract
{
    /** @var string Contains the value contained in the constant */
    protected $value = '';

    /**
     * Retrieves the generic information.
     *
     * Finds out what the name and value is of this constant on top of the
     * information found using the DocBlox_Reflection_DocBlockedAbstract parent
     * method.
     *
     * @param DocBlox_Reflection_TokenIterator $tokens
     *
     * @see DocBlox_Reflection_DocBlockedAbstract::processGenericInformation
     *
     * @return void
     */
    protected function processGenericInformation(
        DocBlox_Reflection_TokenIterator $tokens
    ) {
        if ($tokens->current()->content == 'define') {
            // find the first encapsed string and strip the opening and closing
            // apostrophe
            $name_token = $tokens->gotoNextByType(
                T_CONSTANT_ENCAPSED_STRING, 5, array(',')
            );

            if (!$name_token) {
                $this->log(
                    'Unable to process constant in file ' . $tokens->getFilename()
                    . ' at line ' . $tokens->current()->getLineNumber(),
                    DocBlox_Core_Log::CRIT
                );
                return;
            }

            $this->setName(substr($name_token->content, 1, -1));

            // skip to after the comma
            while ($tokens->current()->content != ',')
            {
                if ($tokens->next() === false) {
                    break;
                }
            }

            // get everything until the closing brace and use that for value,
            // take child parenthesis under consideration
            $value = '';
            $level = 0;
            while (!(($tokens->current()->content == ')') && ($level == -1))) {
                if ($tokens->next() === false) {
                    break;
                }

                switch ($tokens->current()->content) {
                case '(':
                    $level++;
                    break;
                case ')':
                    $level--;
                    break;
                }

                $value .= $tokens->current()->content;
            }

            $this->setValue(trim(substr($value, 0, -1)));
        } else {
            // Added T_NAMESPACE in case anyone uses a constant name NAMESPACE
            // in PHP 5.2.x and tries to parse the code in 5.3.x
            $this->setName(
                $tokens->gotoNextByType(
                    array(T_STRING, T_NAMESPACE),
                    10,
                    array('=')
                )->content
            );

            $this->setValue($this->findDefault($tokens));
        }

        parent::processGenericInformation($tokens);
    }

    /**
     * Stores the value contained in this constant.
     *
     * @param string $value
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the value contained in this Constant.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the XML representation of this object or false if an error occurred.
     *
     * @return string|boolean
     */
    public function __toXml()
    {
        if (!$this->getName()) {
            $xml = new SimpleXMLElement('');
            return $xml->asXML();
        }

        $xml = new SimpleXMLElement('<constant></constant>');
        $xml->name = $this->getName();
        $xml->value = $this->getValue();
        $xml['namespace'] = $this->getNamespace();
        $xml['line'] = $this->getLineNumber();
        $this->addDocblockToSimpleXmlElement($xml);

        return $xml->asXML();
    }

}