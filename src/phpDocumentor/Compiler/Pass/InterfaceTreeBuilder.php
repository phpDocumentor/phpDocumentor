<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\Fqsen;

final class InterfaceTreeBuilder implements CompilerPassInterface
{
    const COMPILER_PRIORITY = 9000;

    /**
     * Returns a textual description of what this pass does for output purposes.
     *
     * Please note that the command line will be truncated to 68 characters (<message> .. 000.000s) so longer
     * descriptions won't have much use.
     *
     * @return string
     */
    public function getDescription()
    {
        return "Adding Parents to child interfaces";
    }

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
    public function execute(ProjectDescriptor $project)
    {
        foreach ($project->getFiles() as $file) {
            /** @var InterfaceDescriptor $interface */
            foreach ($file->getInterfaces()->getAll() as $interface) {
                foreach ($interface->getParent()->getAll() as $parentName) {
                    $parent = $project->getIndexes()->get('interfaces')->get((string)$parentName);
                    if ($parent instanceof InterfaceDescriptor) {
                        $interface->getParent()->set((string)$parentName, $parent);
                    }
                }
            }
        }
    }
}
