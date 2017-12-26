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

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\Type;

/**
 * Base descriptor for tags that have a type associated with them.
 */
abstract class TypedAbstract extends TagDescriptor
{
    /** @var Type $types */
    protected $types;

    /**
     * Sets a list of types associated with this tag.
     *
     * @param Type $types
     *
     * @return void
     */
    public function setTypes(Type $types = null)
    {
        $this->types = $types;
    }

    /**
     * Returns the list of types associated with this tag.
     *
     * @return ?Type
     */
    public function getTypes() /*: ?Type*/
    {
        return $this->types;
    }
}
