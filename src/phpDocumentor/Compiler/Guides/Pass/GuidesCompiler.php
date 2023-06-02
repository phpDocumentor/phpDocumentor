<?php

declare(strict_types=1);

namespace phpDocumentor\Compiler\Guides\Pass;

use phpDocumentor\Compiler\CompilableSubject;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
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

    public function __invoke(CompilableSubject $subject): CompilableSubject
    {
        if ($subject instanceof GuideSetDescriptor === false) {
            return $subject;
        }

        $documents = $this->compiler->run(
            array_map(
                static fn (DocumentDescriptor $descriptor) => $descriptor->getDocumentNode(),
                $subject->getDocuments()->getAll()
            )
        );
        foreach ($documents as $document) {
            $subject->getDocuments()->get($document->getFilePath())->setDocumentNode($document);
        }

        $subject->setMetas(new Metas($this->metas->getAll()));

        return $subject;
    }
}
