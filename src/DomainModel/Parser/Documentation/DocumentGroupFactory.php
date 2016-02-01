<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Parser\Documentation;

use phpDocumentor\DomainModel\Parser\Documentation\DocumentGroup\Definition;
use phpDocumentor\DomainModel\Parser\Documentation\DocumentGroup;

/**
 * Interface for Document group factories
 */
interface DocumentGroupFactory
{
    /**
     * Creates Document group using the provided definition.
     *
     * @param Definition $definition
     *
*@return DocumentGroup
     */
    public function create(Definition $definition);

    /**
     * Will return true when this factory can handle the provided definition.
     *
     * @param Definition $definition
     *
*@return boolean
     */
    public function matches(Definition $definition);
}
