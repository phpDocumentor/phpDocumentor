<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Span;

final class LiteralToken extends SpanToken
{
    private string $value;

    public function __construct(string $id, string $value)
    {
        $this->value = $value;
        parent::__construct(SpanToken::TYPE_LITERAL, $id, []);
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
