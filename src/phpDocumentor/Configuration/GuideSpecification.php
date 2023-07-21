<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration;

use ArrayAccess;

/** @implements ArrayAccess<String, mixed> */
class GuideSpecification implements ArrayAccess
{
    use LegacyArrayAccess;

    public function __construct(private Source $source, private string $output, private string $format)
    {
    }

    public function source(): Source
    {
        return $this->source;
    }

    public function withSource(Source $source): self
    {
        $clone = clone $this;
        $clone->source = $source;

        return $clone;
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
