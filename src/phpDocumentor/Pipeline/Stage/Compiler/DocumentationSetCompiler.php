<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline\Stage\Compiler;

use League\Pipeline\Pipeline;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\VersionDescriptor;

final class DocumentationSetCompiler
{
    /** @param class-string<DocumentationSetDescriptor> $type */
    public function __construct(
        private readonly Pipeline $compilerPipeline,
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
