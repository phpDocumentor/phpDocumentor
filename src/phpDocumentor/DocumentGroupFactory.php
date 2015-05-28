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

namespace phpDocumentor;

/**
 * Interface for Document group factories
 */
interface DocumentGroupFactory
{
    /**
     * Creates Document group using the provided definition.
     *
     * @param DocumentGroupDefinition $definition
     * @return DocumentGroup
     */
    public function create(DocumentGroupDefinition $definition);
}