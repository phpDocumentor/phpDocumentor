<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Traits\HasDescription;
use phpDocumentor\Descriptor\Traits\HasName;
use phpDocumentor\Guides\Nodes\DocumentNode;

final class DocumentDescriptor implements Descriptor
{
    use HasName;
    use HasDescription;

    private DocumentNode $documentNode;
    private string $hash;
    private string $file;
    private string $title;

    public function __construct(
        DocumentNode $documentNode,
        string $hash,
        string $file,
        string $title
    ) {
        $this->documentNode = $documentNode;
        $this->hash = $hash;
        $this->file = $file;
        $this->title = $title;
    }

    public function getDocumentNode(): DocumentNode
    {
        return $this->documentNode;
    }

    public function setDocumentNode(DocumentNode $documentNode): void
    {
        $this->documentNode = $documentNode;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getName(): string
    {
        return $this->title;
    }
}
