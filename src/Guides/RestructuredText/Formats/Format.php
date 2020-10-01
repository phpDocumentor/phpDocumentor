<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Formats;

use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Renderers\NodeRendererFactory;

interface Format
{
    public const HTML  = 'html';
    public const LATEX = 'tex';

    public function getFileExtension() : string;

    /**
     * @return Directive[]
     */
    public function getDirectives() : array;

    /**
     * @return NodeRendererFactory[]
     */
    public function getNodeRendererFactories() : array;
}
