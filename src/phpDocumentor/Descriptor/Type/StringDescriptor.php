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

namespace phpDocumentor\Descriptor\Type;

use phpDocumentor\Descriptor\Interfaces\TypeInterface;

class StringDescriptor implements TypeInterface
{
    /**
     * Returns a human-readable name for this type.
     *
     * @return string
     */
    public function getName()
    {
        return 'string';
    }

    /**
     * Returns a human-readable name for this type.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
