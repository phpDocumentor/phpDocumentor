<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Renderers;

interface FormatListRenderer
{
    public function createElement(string $text, string $prefix) : string;

    /**
     * @return string[]
     */
    public function createList(bool $ordered) : array;
}
