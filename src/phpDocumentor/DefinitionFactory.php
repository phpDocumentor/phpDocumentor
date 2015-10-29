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
namespace phpDocumentor;

/**
 * Interface for Definition factories
 */
interface DefinitionFactory
{
    /**
     * Creates a Definition using the provided options
     *
     * @param array $options
     * @return Definition
     */
    public function create(array $options);
}
