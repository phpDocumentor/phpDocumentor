<?php
/**
 * File contains the DocBlox_Core_Validator_File class
 *
 * @category   DocBlox
 * @package    Parser
 * @subpackage Validator
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
 * @subpackage Validator
 * @author     Ben Selby <benmatselby@gmail.com>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Parser_DocBlock_Validator_File extends DocBlox_Core_Abstract implements DocBlox_Parser_DocBlock_Validator
{
    /**
     * Name of the file being validated
     *
     * @var string
     */
    protected $filename;

    /**
     * Line number of the docblock
     *
     * @var int
     */
    protected $lineNumber;

    /**
     * Docblock for the file
     *
     * @var DocBlox_Reflection_DocBlock
     */
    protected $docblock;

    /**
     * Constructor
     *
     * @param string                           $filename   Filename
     * @param int                              $lineNumber Line number for the docblock
     * @param DocBlox_Reflection_DocBlock|null $docblock   Docbloc
     */
    public function __construct($filename, $lineNumber, $docblock = null)
    {
        $this->filename = $filename;
        $this->lineNumber = $lineNumber;
        $this->docblock = $docblock;
    }

    /**
     * Is the docblock valid?
     *
     * @see DocBlox_Core_Validator::isValid()
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
                'No Page-level DocBlock was found for '.$this->filename.' on line: '.$this->lineNumber, Zend_Log::ERR
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