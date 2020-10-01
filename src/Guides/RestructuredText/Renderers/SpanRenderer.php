<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Renderers;

use phpDocumentor\Guides\RestructuredText\References\ResolvedReference;

interface SpanRenderer
{
    public function emphasis(string $text) : string;

    public function strongEmphasis(string $text) : string;

    public function nbsp() : string;

    public function br() : string;

    public function literal(string $text) : string;

    /**
     * @param mixed[] $attributes
     */
    public function link(?string $url, string $title, array $attributes = []) : string;

    public function escape(string $span) : string;

    /**
     * @param string[] $value
     */
    public function reference(ResolvedReference $reference, array $value) : string;
}
