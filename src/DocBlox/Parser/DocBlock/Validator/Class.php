<?php
/**
 * File contains the DocBlox_Core_Validator_Class class
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
 * This class is responsible for validating the file docblox
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
class DocBlox_Parser_DocBlock_Validator_Class
    extends DocBlox_Parser_DocBlock_Validator_Abstract
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
            return false;
        }

        if (count($this->docblock->getTagsByName('package')) > 1) {
            $this->logParserError(
                'CRITICAL', 'Only one @package tag is allowed', $this->line_number
            );
        }

        if (count($this->docblock->getTagsByName('subpackage')) > 1) {
            $this->logParserError(
                'CRITICAL', 'Only one @subpackage tag is allowed', $this->line_number
            );
        }

        if ($this->docblock->hasTag('subpackage')
            && !$this->docblock->hasTag('package')
        ) {
            $this->logParserError(
                'CRITICAL', 'Cannot have a @subpackage '
                    . 'when a @package tag is not present', $this->line_number
            );
        }

        return $valid;
    }

}