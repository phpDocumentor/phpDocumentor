<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Renderers;

interface FullDocumentNodeRenderer
{
    public function renderDocument() : string;
}
