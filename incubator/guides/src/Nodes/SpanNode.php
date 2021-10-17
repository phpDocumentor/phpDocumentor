<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\Nodes;

use phpDocumentor\Guides\MarkupLanguageParser;
use phpDocumentor\Guides\RestructuredText\Span\SpanParser;
use phpDocumentor\Guides\Span\SpanToken;

use function get_class;
use function implode;
use function is_array;

class SpanNode extends Node
{
    /** @var SpanToken[] */
    protected $tokens;

    /**
     * @param string|string[]|SpanNode $span
     */
    public function __construct(MarkupLanguageParser $parser, $span)
    {
        if (is_array($span)) {
            $span = implode("\n", $span);
        }

        $environment = $parser->getEnvironment();
        if ($span instanceof self) {
            $span = $parser->getNodeRendererFactory()->get(get_class($span))->render($span, $environment);
        }

        $spanProcessor = new SpanParser($parser->getReferenceBuilder());

        parent::__construct($spanProcessor->process($environment, $span));
        $this->tokens = $spanProcessor->getTokens();
    }

    /**
     * @return SpanToken[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }
}
