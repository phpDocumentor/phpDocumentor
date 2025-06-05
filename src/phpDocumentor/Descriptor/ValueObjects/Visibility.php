<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\ValueObjects;

use Stringable;

final class Visibility implements Stringable
{
    public function __construct(
        private VisibilityModifier $read,
        private VisibilityModifier|null $write = null,
    ) {
    }

    public function isAsymmetric(): bool
    {
        return $this->write !== null && $this->read !== $this->write;
    }

    public function __toString(): string
    {
        return $this->read->value;
    }

    public function readModifier(): VisibilityModifier
    {
        return $this->read;
    }

    public function writeModifier(): VisibilityModifier|null
    {
        return $this->write;
    }

    public function read(): string
    {
        return $this->read->value;
    }

    public function write(): string|null
    {
        return $this->write?->value;
    }
}
