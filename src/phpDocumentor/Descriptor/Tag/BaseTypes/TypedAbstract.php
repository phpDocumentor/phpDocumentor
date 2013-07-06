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

use phpDocumentor\Descriptor\TagDescriptor;

abstract class TypedAbstract extends TagDescriptor
{
    protected $types;

    public function setTypes($types)
    {
        $this->types = $types;
    }

    /**
     * @return string[]
     */
    public function getTypes()
    {
        return $this->types;
    }
}
