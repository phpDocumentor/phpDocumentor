<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\Fqsen;

final class ClassTreeBuilder implements CompilerPassInterface
{
    public const COMPILER_PRIORITY = 9000;

    public function getDescription() : string
    {
        return 'Adding Parents to child classes';
    }

    public function execute(ProjectDescriptor $project) : void
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
