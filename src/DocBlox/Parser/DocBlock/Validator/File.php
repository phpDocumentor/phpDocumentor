<?php
/**
 * File contains the DocBlox_Core_Validator_File class
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Parser
 * @subpackage DocBlock_Validators
 * @author     Ben Selby <benmatselby@gmail.com>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
/**
 * This class is responsible for validating the file docbloc
 *
 * @category   DocBlox
 * @package    Parser
 * @subpackage DocBlock_Validators
 * @author     Ben Selby <benmatselby@gmail.com>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Parser_DocBlock_Validator_File
    extends DocBlox_Parser_DocBlock_Validator_Class
{
    /**
     * Is the docblock valid?
     *
     * @see DocBlox_Parser_DocBlock_Validator::isValid()
     *
     * @return boolean
     */
    public function isValid()
    {
        if (!$this->_docblock || !$this->_docblock->hasTag('package')) {
            $this->logParserError(
                'ERROR', 'No Page-level DocBlock '
                . 'was found in file ' . $this->_source->getFilename(), $this->_lineNumber
            );
            return false;
        }

        $valid = parent::isValid();

        if ('' === $this->_docblock->getShortDescription()) {
            $this->logParserError(
                'CRITICAL',
                'No short description for file '
                . $this->_entityName, $this->_lineNumber
            );
        }

        return $valid;
    }
}