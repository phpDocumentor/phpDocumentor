<?php
/**
 * DocBlox
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
 * Base class for DocBlock Validations.
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
abstract class DocBlox_Parser_DocBlock_Validator_Abstract
    extends DocBlox_Parser_Abstract
{
    /**
     * Name of the file being validated.
     *
     * @var string
     */
    protected $filename;

    /**
     * Line number of the docblock
     *
     * @var int
     */
    protected $line_number;

    /**
     * Docblock for the file.
     *
     * @var DocBlox_Reflection_DocBlock
     */
    protected $docblock;

    /**
     * Source element of the DocBlock.
     *
     * @var DocBlox_Reflection_Abstract
     */
    protected $source;

    /**
     * Constructor
     *
     * @param string                           $filename    Filename
     * @param int                              $line_number Line number for
     *  the docblock
     * @param DocBlox_Reflection_DocBlock|null $docblock    Docblock
     * @param DocBlox_Reflection_Abstract|null $source      Source Element.
     */
    public function __construct($filename, $line_number, $docblock = null,
        $source = null)
    {
        $this->filename    = $filename;
        $this->line_number = $line_number;
        $this->docblock    = $docblock;
        $this->source      = $source;
    }

    /**
     * Is the docblock valid?
     *
     * @return boolean
     */
    abstract public function isValid();
}