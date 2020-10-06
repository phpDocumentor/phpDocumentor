<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use phpDocumentor\Guides\Nodes\DocumentNode;

class Documents
{
    /** @var DocumentNode[] */
    private $documents = [];

    /**
     * @return DocumentNode[]
     */
    public function getAll() : array
    {
        return $this->documents;
    }

    public function hasDocument(string $file) : bool
    {
        return isset($this->documents[$file]);
    }

    public function addDocument(string $file, DocumentNode $document) : void
    {
        $this->documents[$file] = $document;
    }
}
