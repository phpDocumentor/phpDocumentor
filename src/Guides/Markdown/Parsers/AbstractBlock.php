<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Markdown\Parsers;

use phpDocumentor\Guides\Markdown\ParserInterface;
use phpDocumentor\Guides\Nodes\Factory;

abstract class AbstractBlock implements ParserInterface
{
    /** @var Factory */
    protected $nodeFactory;
}
