<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Handlers;

use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\RenderContext;

final class RenderDocumentCommand
{
    private DocumentNode $document;
    private RenderContext $renderContext;

    public function __construct(DocumentNode $document, RenderContext $renderContext)
    {
        $this->document = $document;
        $this->renderContext = $renderContext;
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
