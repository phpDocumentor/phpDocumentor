<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes\InlineToken;

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Reflection\Fqsen;

final class PHPReferenceNode extends GenericTextRoleToken
{
    private Descriptor|null $descriptor = null;

    public function __construct(
        string $id,
        private readonly string $nodeType,
        private readonly Fqsen $fqsen,
        private readonly string|null $text = null,
    ) {
        parent::__construct($id, 'phpref', $text ?? (string) $fqsen);
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

    public function setDescriptor(Descriptor|null $descriptor): void
    {
        $this->descriptor = $descriptor;
    }

    public function getDescriptor(): ?Descriptor
    {
        return $this->descriptor;
    }
}
