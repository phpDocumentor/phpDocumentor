<?php
/**
 * phpDocumentor
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

use \phpDocumentor\Plugin\PluginAbstract;

/**
 * Base class for DocBlock Validations.
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
abstract class ValidatorAbstract extends PluginAbstract
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
     * @var \phpDocumentor\Reflection\DocBlock
     */
    protected $docblock;

    /**
     * Source element of the DocBlock.
     *
     * @var \phpDocumentor\Reflection\BaseReflector
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
     * @param \phpDocumentor\Plugin\Plugin            $plugin     Plugin to which this
     *     validator belongs.
     * @param string                                  $name       Name of the "entity"
     * @param \phpDocumentor\Reflection\DocBlock|null $docblock   Docblock
     * @param \phpDocumentor\Reflection\BaseReflector|null  $source     Source Element.
     */
    public function __construct($plugin, $name, $docblock = null,
        $source = null
    ) {
        $this->entityName = $name;
        $this->lineNumber = $docblock
            ? $docblock->line_number : $source->getLineNumber();
        $this->docblock   = $docblock;
        $this->source      = $source;
        parent::__construct(
            $plugin->getEventDispatcher(),
            $plugin->getConfiguration(),
            $plugin->getTranslator()
        );
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
