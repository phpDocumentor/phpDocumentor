<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Tag\BaseTypes;

/**
 * Abstract class for Descriptors with a type and variable name.
 */
abstract class TypedVariableAbstract extends TypedAbstract
{
    /** @var string variableName */
    protected $variableName = '';

    /**
     * Retrieves the variable name stored on this descriptor.
     *
     * @return string
     */
    public function getVariableName()
    {
        return $this->variableName;
    }

    /**
     * Sets the variable name on this descriptor.
     *
     * @param string $variableName
     *
     * @return void
     */
    public function setVariableName($variableName)
    {
        $this->variableName = $variableName;
    }
}
