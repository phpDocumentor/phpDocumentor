<?php

declare(strict_types=1);

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
