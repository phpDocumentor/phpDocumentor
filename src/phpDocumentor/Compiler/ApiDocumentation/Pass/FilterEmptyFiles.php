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

namespace phpDocumentor\Compiler\ApiDocumentation\Pass;

use phpDocumentor\Compiler\ApiDocumentation\ApiDocumentationPass;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Pipeline\Attribute\Stage;

#[Stage(
    'phpdoc.pipeline.api_documentation.compile',
    9500,
    'Filter empty files',
)]
final class FilterEmptyFiles extends ApiDocumentationPass
{
    protected function process(ApiSetDescriptor $subject): ApiSetDescriptor
    {
        $files = $subject->getFiles();

        foreach ($files->getAll() as $file) {
            if (! ($file instanceof FileDescriptor) || ! $file->isEmpty()) {
                continue;
            }

            $files->offsetUnset($file->getPath());
        }

        return $subject;
    }
}
