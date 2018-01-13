<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\Fqsen;

final class ClassTreeBuilder implements CompilerPassInterface
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
        return 'Adding Parents to child classes';
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
            /** @var ClassDescriptor $class */
            foreach ($file->getClasses()->getAll() as $class) {
                if ($class->getParent() instanceof Fqsen) {
                    $parent = $project->getIndexes()->get('classes')->get((string) $class->getParent());
                    if ($parent instanceof ClassDescriptor) {
                        $class->setParent($parent);
                    }
                }

                foreach ($class->getInterfaces()->getAll() as $interface) {
                    $interfaceDescriptor = $project->getIndexes()->get('interfaces')->get((string) $interface);
                    $class->getInterfaces()->set((string) $interface, $interfaceDescriptor);
                }
            }
        }
    }
}
