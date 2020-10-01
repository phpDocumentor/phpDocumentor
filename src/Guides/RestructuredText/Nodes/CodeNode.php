<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Nodes;

class CodeNode extends Node
{
    /** @var string */
    protected $value;

    /** @var bool */
    protected $raw = false;

    /** @var string|null */
    protected $language = null;

    /**
     * @param string[] $lines
     */
    public function __construct(array $lines)
    {
        parent::__construct($this->normalizeLines($lines));
    }

    public function getValue() : string
    {
        return $this->value;
    }

    public function setLanguage(?string $language = null) : void
    {
        $this->language = $language;
    }

    public function getLanguage() : ?string
    {
        return $this->language;
    }

    public function setRaw(bool $raw) : void
    {
        $this->raw = $raw;
    }

    public function isRaw() : bool
    {
        return $this->raw;
    }
}
