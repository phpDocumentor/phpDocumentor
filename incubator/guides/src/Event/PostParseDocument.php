<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Event;

use phpDocumentor\Guides\Nodes\DocumentNode;

final class PostParseDocument
{
    private ?DocumentNode $documentNode;
    private string $fileName;

    public function __construct(string $fileName, ?DocumentNode $documentNode)
    {
        $this->documentNode = $documentNode;
        $this->fileName = $fileName;
    }

    public function getDocumentNode(): ?DocumentNode
    {
        return $this->documentNode;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}
