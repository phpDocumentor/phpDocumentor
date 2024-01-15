<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes\InlineToken;

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Guides\Nodes\Inline\InlineNode;
use phpDocumentor\Reflection\Fqsen;

final class PHPReferenceNode extends InlineNode
{
    private Descriptor|null $descriptor = null;

    public function __construct(
        private readonly string $nodeType,
        private readonly Fqsen $fqsen,
        private readonly string|null $text = null,
    ) {
        parent::__construct('phpref', $text ?? (string) $fqsen);
    }

    public function getText(): string
    {
        return $this->text ?? (string) $this->fqsen;
    }

    public function getNodeType(): string
    {
        return $this->nodeType;
    }

    public function getFqsen(): Fqsen
    {
        return $this->fqsen;
    }

    public function withDescriptor(Descriptor|null $descriptor): self
    {
        $that = clone $this;
        $that->descriptor = $descriptor;

        return $that;
    }

    public function getDescriptor(): Descriptor|null
    {
        return $this->descriptor;
    }

    public function toString(): string
    {
        return $this->fqsen->getName();
    }
}
