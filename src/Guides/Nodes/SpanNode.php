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

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Span\SpanProcessor;
use phpDocumentor\Guides\RestructuredText\Span\SpanToken;
use function implode;
use function is_array;

class SpanNode extends Node
{
    /** @var string */
    protected $value;

    /** @var Environment */
    protected $environment;

    /** @var SpanToken[] */
    protected $tokens;

    /**
     * @param string|string[]|SpanNode $span
     */
    public function __construct(Parser $parser, $span)
    {
        parent::__construct();

        $this->environment = $parser->getEnvironment();

        if (is_array($span)) {
            $span = implode("\n", $span);
        }

        if ($span instanceof self) {
            $span = $span->render();
        }

        $spanProcessor = new SpanProcessor($this->environment, $span);

        $this->value = $spanProcessor->process();
        $this->tokens = $spanProcessor->getTokens();
    }

    public function getValue() : string
    {
        return $this->value;
    }

    /**
     * @return SpanToken[]
     */
    public function getTokens() : array
    {
        return $this->tokens;
    }

    public function getEnvironment() : Environment
    {
        return $this->environment;
    }
}
