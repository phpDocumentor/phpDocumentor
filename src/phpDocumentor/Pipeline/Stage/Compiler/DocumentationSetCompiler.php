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

namespace phpDocumentor\Pipeline\Stage\Compiler;

use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\VersionDescriptor;
use phpDocumentor\Pipeline\PipelineInterface;

final class DocumentationSetCompiler
{
    /** @param class-string<DocumentationSetDescriptor> $type */
    public function __construct(
        private readonly PipelineInterface $compilerPipeline,
        private readonly string $type,
    ) {
    }

    public function __invoke(VersionDescriptor $payload): VersionDescriptor
    {
        $documentationSets = $payload->getDocumentationSets();
        foreach ($documentationSets->filter($this->type) as $documentationSet) {
            $this->compilerPipeline->process($documentationSet);
        }

        return $payload;
    }
}
