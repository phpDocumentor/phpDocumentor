<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;

final class InterfaceTreeBuilder implements CompilerPassInterface
{
    const COMPILER_PRIORITY = 9000;

    public function getDescription(): string
    {
        return 'Adding Parents to child interfaces';
    }

    public function execute(ProjectDescriptor $project): void
    {
        foreach ($project->getFiles() as $file) {
            /** @var InterfaceDescriptor $interface */
            foreach ($file->getInterfaces()->getAll() as $interface) {
                foreach ($interface->getParent()->getAll() as $parentName) {
                    $parent = $project->getIndexes()->get('interfaces')->get((string) $parentName);
                    if ($parent instanceof InterfaceDescriptor) {
                        $interface->getParent()->set((string) $parentName, $parent);
                    }
                }
            }
        }
    }
}
