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
use phpDocumentor\Guides\Nodes\TocNode;

final class DocumentDescriptor implements Descriptor
{
    use HasName;
    use HasDescription;

    private DocumentNode $documentNode;
    private string $hash;
    private string $file;
    private string $title;

    /** @var string[][] */
    private array $titles;

    /** @var TocNode[] */
    private array $tocs;

    /** @var string[] */
    private array $depends;

    /**
     * @param string[][] $titles
     * @param TocNode[] $tocs
     * @param string[] $depends
     */
    public function __construct(
        DocumentNode $documentNode,
        string $hash,
        string $file,
        string $title,
        array $titles,
        array $tocs,
        array $depends
    ) {
        $this->documentNode = $documentNode;
        $this->hash = $hash;
        $this->file = $file;
        $this->title = $title;
        $this->titles = $titles;
        $this->tocs = $tocs;
        $this->depends = $depends;
    }

    public function getDocumentNode(): DocumentNode
    {
        return $this->documentNode;
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

    /** @return string[][] */
    public function getTitles(): array
    {
        return $this->titles;
    }

    /** @return TocNode[] */
    public function getTocs(): array
    {
        return $this->tocs;
    }

    /** @return string[] */
    public function getDepends(): array
    {
        return $this->depends;
    }

    public function getName(): string
    {
        return $this->title;
    }
}
