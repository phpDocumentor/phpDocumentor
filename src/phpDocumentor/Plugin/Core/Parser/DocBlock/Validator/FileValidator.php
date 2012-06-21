<?php
/**
 * File contains the
 * \phpDocumentor\Plugin\Core\Parser\DocBlock\Validator\FileValidator class.
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

namespace phpDocumentor\Plugin\Core\Parser\DocBlock\Validator;

/**
 * This class is responsible for validating the file docbloc
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
class FileValidator extends ClassValidator
{
    /**
     * Is the docblock valid?
     *
     * @see \phpDocumentor\Parser\DocBlock\Validator::isValid()
     *
     * @return boolean
     */
    public function isValid()
    {
        $filename = $this->source->getFilename();
        if (!$this->docblock) {
            $this->logParserError(
                'ERROR', 50007, $this->lineNumber, array($filename)
            );
            return false;
        }

        $valid = true;

        if ('' === $this->docblock->getShortDescription()) {
            $this->logParserError(
                'CRITICAL', 50008, $this->lineNumber, array($this->entityName)
            );
            $valid = false;
        }

        $valid = parent::isValid() && $valid;
        return $valid;
    }
}
