<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Tag\BaseTypes;

abstract class TypedVariableAbstract extends TypedAbstract
{
    protected $variableName = '';

    public function getVariableName()
    {
        return $this->variableName;
    }

    /**
     * @param string $variableName
     */
    public function setVariableName($variableName)
    {
        $this->variableName = $variableName;
    }
}
