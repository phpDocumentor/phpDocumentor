<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Builder;

use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Guides\RestructuredText\Meta\Metas;
use phpDocumentor\Guides\RestructuredText\Nodes\DocumentNode;
use Symfony\Component\Filesystem\Filesystem;
use function dirname;
use function is_dir;
use function sprintf;

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
