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

namespace phpDocumentor\Compiler;

use phpDocumentor\Descriptor\ProjectDescriptor;

/**
 * Represents a single pass / business rule to be executed by the Compiler.
 */
interface CompilerPassInterface
{
    /**
     * Returns a textual description of what this pass does for output purposes.
     *
     * Please note that the command line will be truncated to 68 characters (<message> .. 000.000s) so longer
     * descriptions won't have much use.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Executes a compiler pass.
     *
     * This method will execute the business logic associated with a given compiler pass and allow it to manipulate
     * or consumer the Object Graph using the ProjectDescriptor object.
     *
     * @param ProjectDescriptor $project Representation of the Object Graph that can be manipulated.
     *
     * @return mixed
     */
    public function execute(ProjectDescriptor $project);
}
