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

use phpDocumentor\Guides\Span\SpanToken;

class SpanNode extends Node
{
    /** @var SpanToken[] */
    protected $tokens;

    /** @param SpanToken[] $tokens */
    public function __construct(string $content, array $tokens = [])
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
