<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Formats;

use phpDocumentor\Guides\Renderers\NodeRendererFactory;
use phpDocumentor\Guides\TemplateRenderer;

interface Format
{
    public const HTML  = 'html';
    public const LATEX = 'tex';

    public function getFileExtension() : string;

    /**
     * @return NodeRendererFactory[]
     */
    public function getNodeRendererFactories(TemplateRenderer $templateRenderer) : array;
}
