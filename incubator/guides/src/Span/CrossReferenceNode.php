<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Span;

/**
 * This class should be moved into Nodes, but right now the span parser is producing this.
 * I just want to get started to improve reference handling
 *
 * CrossReferences are references outside a document. As parsing is file based normal references are in document,
 * refering to other documents.
 *
 * Supported formats
 * :role:`foo`
 * :role:`foo/subdoc#anchor`
 * :domain:role:`foo`
 * :role:`custom text <foo>`
 * :role:`custom text <foo/subdoc#anchor>`
 *
 * Cross references are resolved during rendering? -> Should be compiler.
 */
class CrossReferenceNode extends SpanToken
{
    /** @var string */
    private $id;

    /** @var string */
    private $literal;

    /** @var string|null */
    private $role;

    /** @var string|null */
    private $domain;

    /** @var string|null */
    private $anchor;

    /** @var string|null */
    private $text;

    public function __construct(
        string $id,
        string $role,
        string $literal,
        ?string $anchor = null,
        ?string $text = null,
        ?string $domain = null
    ) {
        $this->id = $id;
        $this->literal = $literal;
        $this->role = $role;
        $this->domain = $domain;
        $this->anchor = $anchor;
        $this->text = $text;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->literal;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function getAnchor(): ?string
    {
        return $this->anchor;
    }

    public function getText(): string
    {
        return $this->text ?? $this->literal;
    }
}
