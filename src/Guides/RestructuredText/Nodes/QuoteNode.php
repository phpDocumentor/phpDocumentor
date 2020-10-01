<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Nodes;

class QuoteNode extends Node
{
    /** @var DocumentNode */
    protected $value;

    public function __construct(DocumentNode $documentNode)
    {
        parent::__construct($documentNode);
    }

    public function getValue() : DocumentNode
    {
        return $this->value;
    }
}
