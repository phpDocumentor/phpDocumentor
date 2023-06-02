<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline\Stage\Compiler;

use League\Pipeline\Pipeline;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\VersionDescriptor;

final class DocumentationSetCompiler
{
    private Pipeline $compilerPipeline;

    /**
     * @param class-string<DocumentationSetDescriptor> $type
     */
    public function __construct(
        Pipeline $compilerPipeline,
        private readonly string $type,
    ) {
        $this->compilerPipeline = $compilerPipeline;
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
