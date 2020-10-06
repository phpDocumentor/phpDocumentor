<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Formats;

use phpDocumentor\Guides\Renderers\NodeRendererFactory;

interface Format
{
    public const HTML  = 'html';
    public const LATEX = 'tex';

    public function getFileExtension() : string;

    /**
     * @return NodeRendererFactory[]
     */
    public function getNodeRendererFactories() : array;
}
