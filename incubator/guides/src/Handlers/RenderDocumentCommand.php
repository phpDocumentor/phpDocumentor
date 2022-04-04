<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Handlers;

use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\RenderContext;

final class RenderDocumentCommand
{
    private DocumentNode $document;
    private RenderContext $renderContext;
    private string $target;

    public function __construct(DocumentNode $document, RenderContext $renderContext, string $target)
    {
        $this->document = $document;
        $this->renderContext = $renderContext;
        $this->target = $target;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getDocument(): DocumentNode
    {
        return $this->document;
    }

    public function getContext(): RenderContext
    {
        return $this->renderContext;
    }
}
