<?php
/**
 * File contains the DocBlox_Core_Validator_Method class
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
 * This class is responsible for validating the method doc block
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
class DocBlox_Plugin_Core_Parser_DocBlock_Validator_Method
    extends DocBlox_Plugin_Core_Parser_DocBlock_Validator_Abstract
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
        $valid = true;

        if (null == $this->docblock) {
            $this->logParserError(
                'ERROR',
                'No DocBlock was found for method '
                . $this->entityName, $this->lineNumber
            );
            return false;
        }

        if ('' === $this->docblock->getShortDescription()) {
            $this->logParserError(
                'CRITICAL',
                'No short description for method '
                . $this->entityName, $this->lineNumber
            );
        }

        return $valid;
    }
}