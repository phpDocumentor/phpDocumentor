<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\LaTeX;

use IteratorAggregate;
use phpDocumentor\Guides\NodeRenderers\NodeRendererFactory;
use phpDocumentor\Guides\RestructuredText\OutputFormat;

class LaTeXFormat extends OutputFormat
{
    /** @var NodeRendererFactory */
    private $nodeRendererFactory;

    public function __construct(
        string $fileExtension,
        IteratorAggregate $directives,
        NodeRendererFactory $nodeRendererFactory
    ) {
        parent::__construct($fileExtension, $directives);
        $this->nodeRendererFactory = $nodeRendererFactory;
    }

    public function getNodeRendererFactory(): NodeRendererFactory
    {
        return $this->nodeRendererFactory;
    }
}
