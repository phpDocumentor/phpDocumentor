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
abstract class DocBlox_Plugin_Core_Parser_DocBlock_Validator_Abstract
    extends DocBlox_Parser_Abstract
{
    /**
     * Name of the "entity" being validated.
     *
     * @var string
     */
    protected $entityName;

    /**
     * Line number of the docblock
     *
     * @var int
     */
    protected $lineNumber;

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
     * Array of options that may or may not be used whilst validating
     *
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param string                           $name       Name of the "entity"
     * @param int                              $lineNumber Line number for
     * @param DocBlox_Reflection_DocBlock|null $docblock   Docblock
     * @param DocBlox_Reflection_Abstract|null $source     Source Element.
     */
    public function __construct($name, $lineNumber, $docblock = null,
        $source = null
    ) {
        $this->entityName = $name;
        $this->lineNumber = $lineNumber;
        $this->docblock   = $docblock;
        $this->source      = $source;
    }

    /**
     * Set the options that may be used whilst validating the docblocks.
     * Can contain configuration as long as each validator knows how to
     * interrogate it
     *
     * @param array $options Options that may be used during validation
     *
     * @return void
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Is the docblock valid?
     *
     * @return boolean
     */
    abstract public function isValid();
}