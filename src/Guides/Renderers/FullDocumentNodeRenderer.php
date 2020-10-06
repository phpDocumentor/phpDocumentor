<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers;

interface FullDocumentNodeRenderer
{
    public function renderDocument() : string;
}
