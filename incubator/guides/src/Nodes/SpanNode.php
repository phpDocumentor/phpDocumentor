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

use function implode;
use function is_array;

class SpanNode extends Node
{
    /** @var SpanToken[] */
    protected $tokens;

    /**
     * @param string|string[] $span
     */
    public static function create(MarkupLanguageParser $parser, $span): self
    {
        if (is_array($span)) {
            $span = implode("\n", $span);
        }

        $environment = $parser->getEnvironment();
        $spanProcessor = new SpanParser();

        return new self($spanProcessor->process($environment, $span), $spanProcessor->getTokens());
    }

    /** @param SpanToken[] $tokens */
    public function __construct(string $content, array $tokens)
    {
        parent::__construct($content);
        $this->tokens = $tokens;
    }

    /**
     * @return SpanToken[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }
}
