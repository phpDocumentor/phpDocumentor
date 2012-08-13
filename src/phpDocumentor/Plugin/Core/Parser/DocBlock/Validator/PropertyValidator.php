<?php
/**
 * File contains the
 * \phpDocumentor\Plugin\Core\Parser\DocBlock\Validator\PropertyValidator class.
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
 * This class is responsible for validating a properties docblock.
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
class PropertyValidator extends ValidatorAbstract
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
        $valid = true;

        if (!$this->docblock) {
            $this->logParserError(
                'ERROR', 50018, $this->lineNumber, array($this->entityName)
            );
            return false;
        }

        if ('' === $this->docblock->getShortDescription()) {
            foreach($this->docblock->getTagsByName('var') as $varTag) {
                if ('' !== $varTag->getDescription()) {
                    return true;
                }
            }
            $this->logParserError(
                'CRITICAL', 50019, $this->lineNumber, array($this->entityName)
            );
        }

        return $valid;
    }
}
