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

namespace phpDocumentor\Project\Version;

use phpDocumentor\Project\Version;

/**
 * Factory to create Version
 */
final class Factory
{
    /**
     * Create a version entity from the given definition.
     *
     * @param Definition $definition
     * @return Version
     */
    public function create(Definition $definition)
    {
        return new Version($definition->getVersionNumber());
    }
}
