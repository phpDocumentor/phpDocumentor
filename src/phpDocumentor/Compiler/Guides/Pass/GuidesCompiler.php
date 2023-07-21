<?php

declare(strict_types=1);

namespace phpDocumentor\Compiler\Guides\Pass;

use phpDocumentor\Compiler\CompilableSubject;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Guides\Compiler\Compiler;
use phpDocumentor\Guides\Compiler\CompilerContext;

use function array_map;

final class GuidesCompiler implements CompilerPassInterface
{
    public function __construct(private readonly Compiler $compiler)
    {
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
                $subject->getDocuments()->getAll(),
            ),
            new CompilerContext($subject->getGuidesProjectNode()),
        );
        foreach ($documents as $document) {
            $subject->getDocuments()->get($document->getFilePath())->setDocumentNode($document);
        }

        return $subject;
    }
}
