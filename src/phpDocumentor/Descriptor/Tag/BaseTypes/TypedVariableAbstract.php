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

abstract class TypedVariableAbstract extends TypedDescriptorAbstract
{
    protected $variableName = '';

    public function __construct($reflectionTag)
    {
        parent::__construct($reflectionTag);

        $this->variableName = $reflectionTag->getVariableName();
    }

    public function getVariableName()
    {
        return $this->variableName;
    }
}
