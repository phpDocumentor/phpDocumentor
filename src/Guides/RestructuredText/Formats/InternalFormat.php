<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Formats;

use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Renderers\NodeRendererFactory;

class InternalFormat implements Format
{
    /** @var Format */
    private $format;

    /** @var Directive[]|null */
    private $directives;

    /** @var NodeRendererFactory[]|null */
    private $nodeRendererFactories;

    public function __construct(Format $format)
    {
        $this->format = $format;
    }

    public function getFileExtension() : string
    {
        return $this->format->getFileExtension();
    }

    /**
     * @return Directive[]
     */
    public function getDirectives() : array
    {
        if ($this->directives === null) {
            $this->directives = $this->format->getDirectives();
        }

        return $this->directives;
    }

    /**
     * @return NodeRendererFactory[]
     */
    public function getNodeRendererFactories() : array
    {
        if ($this->nodeRendererFactories === null) {
            $this->nodeRendererFactories = $this->format->getNodeRendererFactories();
        }

        return $this->nodeRendererFactories;
    }
}
