<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Descriptor\Validator;

use phpDocumentor\Reflection\BaseReflector;
use phpDocumentor\Reflection\DocBlock;

/**
 * Base class for DocBlock Validations.
 */
abstract class ValidatorAbstract
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
     * @param string             $name     Name of the "entity"
     * @param DocBlock|null      $docblock Docblock
     * @param BaseReflector|null $source   Source Element.
     */
    public function __construct($name, $docblock = null, $source = null)
    {
        $this->entityName = $name;
        $this->lineNumber = $docblock
            ? $docblock->getLocation()->getLineNumber()
            : $source->getLineNumber();
        $this->docblock   = $docblock;
        $this->source     = $source;
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

    abstract public function isValid(BaseReflector $element);

    /**
     * Returns the configuration for this object.
     *
     * @return \phpDocumentor\Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
