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

use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\Nodes\TocNode;

final class DocumentDescriptor implements Descriptor
{
    /** @var DocumentNode */
    private $documentNode;

    /** @var string */
    private $hash;

    /** @var string */
    private $file;

    /** @var string */
    private $title;

    /** @var string[][] */
    private $titles;

    /** @var TocNode[] */
    private $tocs;

    /** @var string[] */
    private $depends;

    /** @var string[] */
    private $links;

    /** @var array<string|SpanNode> */
    private $variables;

    /**
     * @param string[][] $titles
     * @param TocNode[] $tocs
     * @param string[] $depends
     * @param string[] $links
     * @param array<string|SpanNode> $variables
     */
    public function __construct(
        DocumentNode $documentNode,
        string $hash,
        string $file,
        string $title,
        array $titles,
        array $tocs,
        array $depends,
        array $links,
        array $variables
    ) {
        $this->documentNode = $documentNode;
        $this->hash = $hash;
        $this->file = $file;
        $this->title = $title;
        $this->titles = $titles;
        $this->tocs = $tocs;
        $this->depends = $depends;
        $this->links = $links;
        $this->variables = $variables;
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

    /** @return string[] */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * Returns variables collected during the parsing of s document.
     *
     * The 'replace' directive, for example, stores the replacement as a variable on the Document. To be able to
     * access the variables collected during parsing, we can store a series of variables here.
     *
     * @return SpanNode[]|string[]
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getName(): string
    {
        return $this->title;
    }

    public function getDescription(): ?DocBlock\DescriptionDescriptor
    {
        return null;
    }
}
