<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;

final class RemoveSourcecode implements CompilerPassInterface
{
    public const COMPILER_PRIORITY = 2000;

    public function getDescription(): string
    {
        return 'Removing sourcecode from file descriptors';
    }

    public function execute(ProjectDescriptor $project): void
    {
        foreach ($project->getVersions() as $version) {
            foreach ($version->getDocumentationSets() as $documentationSet) {
                if (
                    !$documentationSet instanceof ApiSetDescriptor ||
                    $documentationSet->getSettings()['include-source']
                ) {
                    continue;
                }

                foreach ($project->getFiles() as $file) {
                    $file->setSource(null);
                }
            }
        }
    }
}
