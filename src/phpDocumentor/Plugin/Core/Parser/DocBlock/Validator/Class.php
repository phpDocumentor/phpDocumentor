<?php
/**
 * File contains the phpDocumentor_Core_Validator_Class class
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage DocBlock_Validators
 * @author     Ben Selby <benmatselby@gmail.com>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
/**
 * This class is responsible for validating the class docbloc
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage DocBlock_Validators
 * @author     Ben Selby <benmatselby@gmail.com>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class phpDocumentor_Plugin_Core_Parser_DocBlock_Validator_Class
    extends phpDocumentor_Plugin_Core_Parser_DocBlock_Validator_Abstract
{
    /**
     * Is the docblock valid?
     *
     * @see phpDocumentor_Parser_DocBlock_Validator::isValid()
     *
     * @return boolean
     */
    public function isValid()
    {
        $valid = true;

        if (null == $this->docblock) {
            $this->logParserError(
                'ERROR', 50000, $this->lineNumber, array($this->entityName)
            );
            return false;
        }

        if (count($this->docblock->getTagsByName('package')) > 1) {
            $this->logParserError('CRITICAL', 50001, $this->lineNumber);
        }

        if (count($this->docblock->getTagsByName('subpackage')) > 1) {
            $this->logParserError('CRITICAL', 50002, $this->lineNumber);
        }

        if ($this->docblock->hasTag('subpackage')
            && !$this->docblock->hasTag('package')
        ) {
            $this->logParserError('CRITICAL', 50004, $this->lineNumber);
        }

        if ('' === $this->docblock->getShortDescription()) {
            $this->logParserError(
                'CRITICAL', 50005, $this->lineNumber, array($this->entityName)
            );
        }

        return $valid;
    }
}