<?php

declare(strict_types=1);

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Guides\Compiler\Compiler;
use phpDocumentor\Guides\Metas;

use function array_map;

final class GuidesCompiler implements CompilerPassInterface
{
    private Compiler $compiler;
    private Metas $metas;

    public function __construct(Compiler $compiler, Metas $metas)
    {
        $this->compiler = $compiler;
        $this->metas = $metas;
    }

    public function getDescription(): string
    {
        return 'Compiling guides';
    }

    public function execute(ProjectDescriptor $project): void
    {
        foreach ($project->getVersions() as $version) {
            foreach ($version->getDocumentationSets() as $documentationSet) {
                if (!($documentationSet instanceof GuideSetDescriptor)) {
                    continue;
                }

               // $this->metas->setMetaEntries([]);
                $documents = $this->compiler->run(
                    array_map(
                        static fn (DocumentDescriptor $descriptor) => $descriptor->getDocumentNode(),
                        $documentationSet->getDocuments()->getAll()
                    )
                );
                foreach ($documents as $document) {
                    $documentationSet->getDocuments()->get($document->getFilePath())->setDocumentNode($document);
                }

                $documentationSet->setMetas(new Metas($this->metas->getAll()));
            }
        }
    }
}
