<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Formats;

use phpDocumentor\Guides\Formats\Format as BaseFormat;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;

interface Format extends BaseFormat
{
    /**
     * @return Directive[]
     */
    public function getDirectives() : array;
}
