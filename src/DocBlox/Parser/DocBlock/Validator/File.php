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
class DocBlox_Parser_DocBlock_Validator_File extends DocBlox_Parser_DocBlock_Validator_Abstract
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

        if (!$this->docblock->hasTag('package')) {
            $valid = false;
            $this->log(
                'No Page-level DocBlock was found for '.$this->filename.' on line: '.$this->line_number, Zend_Log::ERR
            );
        }

        if (count($this->docblock->getTagsByName('package')) > 1) {
            $this->log(
                'File cannot have more than one @package tag in '.$this->filename,
                Zend_Log::CRIT
            );
        }

        if ($this->docblock->hasTag('subpackage') && !$this->docblock->hasTag('package')) {
            $this->log(
                'File cannot have a @subpackage when a @package tag is not present in '.$this->filename,
                Zend_Log::CRIT
            );
        }

        return $valid;
    }
}