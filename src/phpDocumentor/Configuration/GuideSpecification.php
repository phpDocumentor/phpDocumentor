<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration;

use ArrayAccess;

/**
 * @implements ArrayAccess<String, mixed>
 */
class GuideSpecification implements ArrayAccess
{
    use LegacyArrayAccess;

    /** @var Source */
    private $source;

    /** @var string */
    private $output;

    /** @var string */
    private $format;

    public function __construct(Source $source, string $output, string $format)
    {
        $this->source = $source;
        $this->output = $output;
        $this->format = $format;
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
